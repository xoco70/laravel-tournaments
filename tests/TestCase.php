<?php

namespace Xoco70\KendoTournaments\Tests;

use Illuminate\Foundation\Auth\User;
use Orchestra\Database\ConsoleServiceProvider;
use Orchestra\Testbench\BrowserKit\TestCase as BaseTestCase;
use stdClass;
use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\ChampionshipSettings;
use Xoco70\KendoTournaments\Models\Competitor;
use Xoco70\KendoTournaments\Models\Fight;
use Xoco70\KendoTournaments\Models\FightersGroup;
use Xoco70\KendoTournaments\Models\Tournament;
use Xoco70\KendoTournaments\TournamentsServiceProvider;

abstract class TestCase extends BaseTestCase
{
    const DB_HOST = '127.0.0.1';
    const DB_NAME = 'plugin';
    const DB_USERNAME = 'root';
    const DB_PASSWORD = '';

    protected $root;
    protected $baseUrl = "http://tournament-plugin.dev";

    protected $settings, $users;
    protected $championshipWithComp, $championshipWithTeam;

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
        $this->root = new User();
        $this->makeSureDatabaseExists();
        parent::setUp();

        $this->withFactories(__DIR__ . '/../database/factories');

        $this->tournament = Tournament::with(
            'competitors',
            'teams',
            'championshipSettings'
        )->first();


        $this->championshipWithComp = Championship::with(
            'teams', 'users', 'category', 'settings', 'fightersGroups.fights'
        )
            ->find($this->tournament->championships[0]->id);
        $this->championshipWithTeam = Championship::with(
            'teams', 'users', 'category', 'settings', 'fightersGroups.fights'
        )
            ->find($this->tournament->championships[1]->id);
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
            factory(Competitor::class)->create(
                [
                    'user_id' => $user->id,
                    'championship_id' => $championship->id,
                    'confirmed' => 1,
                ]
            );
        }
    }

    public function generateTreeWithUI($setting)
    {

        $this->visit('/kendo-tournaments')
            ->select($setting->hasPreliminary, 'hasPreliminary')
            ->select($setting->isTeam, 'isTeam')
            ->select($setting->numArea, 'fightingAreas')
            ->select(
                $setting->hasPlayOff
                    ? ChampionshipSettings::PLAY_OFF
                    : ChampionshipSettings::DIRECT_ELIMINATION, 'treeType'
            )
            ->select($setting->preliminaryGroupSize, 'preliminaryGroupSize')
            ->select($setting->numCompetitors, 'numFighters');


        $this->press('save');
    }

    /**
     * @param $championship
     * @param $numArea
     * @param $numFighters
     * @param $numGroupsExpected
     * @param $currentTest
     */
    protected function checkGroupsNumber($championship, $numArea, $numFighters, $numGroupsExpected, $currentTest)
    {
        $count = FightersGroup::where('championship_id', $championship->id)
            ->where('round', 1)
            ->count();

        if ((int)($numFighters / $numArea) <= 1) {
            $this->assertTrue($count == 0);
            return;
        }
        $expected = $numGroupsExpected[$numFighters - 1];
        if ($count != $expected) {
            dd(
                ['Method' => $currentTest,
                    'championship' => $championship->id,
                    'NumCompetitors' => $numFighters,
                    'preliminaryGroupSize' => $championship->getSettings()->preliminaryGroupSize,
                    'NumArea' => $numArea,
                    'Real' => $count,
                    'Excepted' => $expected,
                    'numGroupsExpected[' . ($numFighters - 1) . ']' => $numGroupsExpected[$numFighters - 1] . ' / ' . $numArea,
                ]
            );
        }
        $this->assertTrue($count == $expected);
    }

    /**
     * @param $championship
     * @param $numArea
     * @param $numCompetitors
     * @param $numFightsExpected
     * @param $methodName
     */
    protected function checkFightsNumber($championship, $numArea, $numCompetitors, $numFightsExpected, $methodName)
    {
        $groupSize = $championship->hasPreliminary() ? $championship->settings->preliminaryGroupSize : 2;
        $count = $this->getFightsCount($championship);

        if ((int)($numCompetitors / $numArea) <= 1
            || $numCompetitors / ($groupSize * $numArea) < 1) {

            $this->assertTrue($count == 0);
            return;
        }

        if ($count != $numFightsExpected) {
            dd(['Method' => $methodName,
                'NumCompetitors' => $numCompetitors,
                'NumArea' => $numArea,
                'Real' => $count,
                'Excepted' => $numFightsExpected,
            ]);
        }
        $this->assertTrue($count == $numFightsExpected);
    }

    /**
     * @param $championship
     * @param $area
     * @return mixed
     */
    protected function getFightsCount($championship)
    {
        $groupsId = FightersGroup::where('championship_id', $championship->id)
//            ->where('area', $area)
            ->where('round', 1)
            ->select('id')
            ->pluck('id')->toArray();

        $count = Fight::whereIn('fighters_group_id', $groupsId)->count();
        return $count;
    }

    /**
     * @param $numArea
     * @param $numCompetitors
     * @param $team
     * @return stdClass
     */
    protected function createSetting($numArea, $numCompetitors, $team, $hasPreliminary, $preliminaryGroupSize): stdClass
    {
        $setting = new stdClass;
        $setting->numArea = $numArea;
        $setting->numCompetitors = $numCompetitors;
        $setting->preliminaryGroupSize = $preliminaryGroupSize;
        $setting->hasPlayOff = false;
        $setting->hasPreliminary = $hasPreliminary;
        $setting->isTeam = $team;
        return $setting;
    }
}