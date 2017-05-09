<?php

namespace Xoco70\KendoTournaments\Tests;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Orchestra\Database\ConsoleServiceProvider;
use Orchestra\Testbench\BrowserKit\TestCase as BaseTestCase;
use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\ChampionshipSettings;
use Xoco70\KendoTournaments\Models\Competitor;
use Xoco70\KendoTournaments\Models\Fight;
use Xoco70\KendoTournaments\Models\FightersGroup;
use Xoco70\KendoTournaments\Models\Tournament;
use Xoco70\KendoTournaments\TournamentsServiceProvider;
use Xoco70\KendoTournaments\TreeGen\TreeGen;

abstract class TestCase extends BaseTestCase
{
    const DB_HOST = '127.0.0.1';
    const DB_NAME = 'plugin';
    const DB_USERNAME = 'root';
    const DB_PASSWORD = '';

    protected $root;
    protected $baseUrl = "http://tournament-plugin.dev";

    protected function getPackageProviders($app)
    {
        return [TournamentsServiceProvider::class,
            ConsoleServiceProvider::class,];
    }

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->root = new \Illuminate\Foundation\Auth\User();
        $this->makeSureDatabaseExists();
        parent::setUp();

        $this->withFactories(__DIR__ . '/../database/factories');
    }

    private function makeSureDatabaseExists()
    {
        $this->runQuery('CREATE DATABASE IF NOT EXISTS ' . static::DB_NAME);
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', [
            'driver' => 'mysql',
            'host' => $_SERVER['DB_HOST'] ?? static::DB_HOST,
            'database' => $_SERVER['DB_NAME'] ?? static::DB_NAME,
            'username' => $_SERVER['DB_USERNAME'] ?? static::DB_USERNAME,
            'password' => $_SERVER['DB_PASSWORD'] ?? static::DB_PASSWORD,
            'prefix' => 'ken_',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'strict' => false,
        ]);
    }


    /**
     * @param $query
     * return void
     */
    private function runQuery($query)
    {
        $dbUsername = static::DB_USERNAME;
        $dbPassword = static::DB_PASSWORD;
        $command = "mysql -u $dbUsername ";
        $command .= $dbPassword ? " -p$dbPassword" : '';
        $command .= " -e '$query'";
        exec($command . ' 2>/dev/null');
    }


    /**
     * @param $users
     */
    public function makeCompetitors($championship, $users)
    {
        foreach ($users as $user) {
            factory(Competitor::class)->create([
                'user_id' => $user->id,
                'championship_id' => $championship->id,
                'confirmed' => 1,]);
        }
    }

    public function generateTreeWithUI($numAreas, $numCompetitors, $preliminaryGroupSize, $hasPlayOff, $hasPreliminary)
    {

        $this->visit('/kendo-tournaments')
            ->select($hasPreliminary, 'hasPreliminary')
            ->select($numAreas, 'fightingAreas')
            ->select($hasPlayOff
                ? ChampionshipSettings::PLAY_OFF
                : ChampionshipSettings::DIRECT_ELIMINATION, 'treeType')
            ->select($preliminaryGroupSize, 'preliminaryGroupSize')
            ->select($numCompetitors, 'numFighters');


        $this->press('save');
    }

    /**
     * @param $championship
     * @param $numArea
     * @param $numCompetitors
     * @param $numGroupsExpected
     * @param $currentTest
     */
    protected function checkGroupsNumber($championship, $numArea, $numCompetitors, $numGroupsExpected, $currentTest)
    {
        for ($area = 1; $area <= $numArea; $area++) {
            $count = FightersGroup::where('championship_id', $championship->id)
                ->where('area', $area)
                ->where('round', 1)
                ->count();

            if ((int)($numCompetitors / $numArea) <= 1) {
                $this->assertTrue($count == 0);
            } else {
                $expected = (int)($numGroupsExpected[$numCompetitors - 1] / $numArea);

                if ($count != $expected) {
                    dd(['Method' => $currentTest,
                        'NumCompetitors' => $numCompetitors,
                        'NumArea' => $numArea,
                        'Real' => $count,
                        'Excepted' => $expected,
                        'numGroupsExpected[' . ($numCompetitors - 1) . ']' => $numGroupsExpected[$numCompetitors - 1] . ' / ' . $numArea]);
                }
                $this->assertTrue($count == $expected);
            }
        }
    }

    /**
     * @param $championship
     * @param $numArea
     * @param $numCompetitors
     * @param $numFightsExpected
     * @param $currentTest
     */
    protected function checkFightsNumber($championship, $numArea, $numCompetitors, $numFightsExpected, $currentTest)
    {
        for ($area = 1; $area <= $numArea; $area++) {
            $groupsId = FightersGroup::where('championship_id', $championship->id)
                ->where('area', $area)
                ->where('round', 1)
                ->select('id')
                ->pluck('id')->toArray();

            $count = Fight::whereIn('fighters_group_id', $groupsId)->count();


            if ((int)($numCompetitors / $numArea) <= 1) {
                $this->assertTrue($count == 0);
            } else {
                $log = ceil(log($numFightsExpected[$numCompetitors - 1], 2));

                $expected = pow(2, $log) / $numArea;


                if ($count != $expected) {
                    dd(['Method' => $currentTest,
                        'NumCompetitors' => $numCompetitors,
                        'NumArea' => $numArea,
                        'Real' => $count,
                        'Excepted' => $expected,
                        'numGroupsExpected[' . ($numCompetitors - 1) . ']' => "2 pow " . $log]);
                }
                $this->assertTrue($count == $expected);
            }
        }
    }
    /**
     * @param $treeType
     * @param $hasPreliminary
     * @param $fightingAreas
     * @param $numFighters
     */
    public function create_tree($treeType, $hasPreliminary, $fightingAreas, $numFighters)
    {
        $user = User::find(1);
        Auth::login($user);
        $tournament = factory(Tournament::class)->create(['user_id' => 1]);
        $championship = factory(Championship::class)->create(
            ['tournament_id' => $tournament->id,
                'category_id' => 1 // Not Teams,
            ]
        );
        $settings = factory(ChampionshipSettings::class)->create(
            ['championship_id' => $championship->id,
                'fightingAreas' => $fightingAreas,
                'hasPreliminary' => $hasPreliminary,
                'treeType' => $treeType
            ]);
        $settings->championship_id = $championship->id;

        $championship->settings = $settings;
        $users = factory(User::class, $numFighters)->create();
        $competitors = $this->createCompetitors($users, $championship);

        $generation = new TreeGen($championship, null);

        $competitors = $generation->adjustFightersGroupWithByes($competitors, $fighterGroups);
        $competitors = $competitors->chunk(count($competitors) / $fightingAreas);

        $generation->pushEmptyGroupsToTree($numFighters);
        $generation->generateGroupsForRound($competitors, $fightingAreas, 1, $shuffle = 0);
        FightersGroup::generateFights($championship);
        // For Now, We don't generate fights when Preliminary
        if ($championship->isDirectEliminationType() && !$championship->hasPreliminary()) {
            FightersGroup::generateNextRoundsFights($championship);
        }

    }
    /**
     * @param $users
     * @param $championship
     * @return Collection
     */
    private function createCompetitors($users, $championship)
    {
        $competitors = new Collection;
        $users->each(function ($user) use ($championship, $competitors) {
            $competitor = factory(Competitor::class)->create(
                ['user_id' => $user->id,
                    'championship_id' => $championship->id,
                    'short_id' =>$user->id,
                ]);
            $competitors->push($competitor);
        });
        return $competitors;

    }
}