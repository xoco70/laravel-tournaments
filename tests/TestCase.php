<?php

namespace Xoco70\LaravelTournaments\Tests;

use Faker\Factory;
use Illuminate\Foundation\Auth\User;
use Orchestra\Database\ConsoleServiceProvider;
use Orchestra\Testbench\BrowserKit\TestCase as BaseTestCase;
use stdClass;
use Xoco70\LaravelTournaments\Models\Category;
use Xoco70\LaravelTournaments\Models\Championship;
use Xoco70\LaravelTournaments\Models\ChampionshipSettings;
use Xoco70\LaravelTournaments\Models\Competitor;
use Xoco70\LaravelTournaments\Models\Fight;
use Xoco70\LaravelTournaments\Models\FightersGroup;
use Xoco70\LaravelTournaments\Models\Tournament;
use Xoco70\LaravelTournaments\Models\Venue;
use Xoco70\LaravelTournaments\TournamentsServiceProvider;

abstract class TestCase extends BaseTestCase
{
    const DB_HOST = '127.0.0.1';
    const DB_NAME = 'plugin';
    const DB_USERNAME = 'root';
    const DB_PASSWORD = '';

    protected $root;
    protected $baseUrl = 'http://tournament-plugin.dev';

    protected $settings;
    protected $users;
    protected $championshipWithComp;
    protected $championshipWithTeam;

    protected function getPackageProviders($app)
    {
        return [TournamentsServiceProvider::class,
            ConsoleServiceProvider::class, ];
    }

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->root = new User();
        $this->makeSureDatabaseExists();
        parent::setUp();
        $this->artisan('migrate', ['--database' => 'testbench']);
        $this->withFactories(__DIR__.'/../database/factories');
        $this->initialSeed();
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
        $this->runQuery('CREATE DATABASE IF NOT EXISTS '.static::DB_NAME);
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
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
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
        exec($command.' 2>/dev/null');
    }

    /**
     * @param $users
     */
    public function makeCompetitors($championship, $users)
    {
        foreach ($users as $user) {
            factory(Competitor::class)->create(
                [
                    'user_id'         => $user->id,
                    'championship_id' => $championship->id,
                    'confirmed'       => 1,
                ]
            );
        }
    }

    public function generateTreeWithUI($setting)
    {
        $this->visit('/laravel-tournaments')
            ->select($setting->hasPreliminary, 'hasPreliminary')
            ->select($setting->isTeam, 'isTeam')
            ->select($setting->numArea, 'fightingAreas')
            ->select(
                $setting->hasPlayOff
                    ? ChampionshipSettings::PLAY_OFF
                    : ChampionshipSettings::DIRECT_ELIMINATION, 'treeType'
            )
            ->select($setting->preliminaryGroupSize, 'preliminaryGroupSize')
            ->select($setting->numFighters, 'numFighters');

        $this->press('save');
    }

    /**
     * @param $championship
     * @param $setting
     * @param $currentTest
     */
    protected function checkGroupsNumber($championship, $setting, $numGroupsExpected, $currentTest)
    {
        $count = FightersGroup::where('championship_id', $championship->id)
            ->where('round', 1)
            ->count();

        if ((int) ($setting->numFighters / $setting->numArea) <= 1) {
            $this->assertTrue($count == 0);

            return;
        }
        $expected = $numGroupsExpected[$setting->numFighters - 1];
        if ($count != $expected) {
            dd(
                ['Method'                                                    => $currentTest,
                    'championship'                                           => $championship->id,
                    'NumCompetitors'                                         => $setting->numFighters,
                    'preliminaryGroupSize'                                   => $championship->getSettings()->preliminaryGroupSize,
                    'NumArea'                                                => $setting->numArea,
                    'isTeam'                                                 => $setting->isTeam,
                    'Real'                                                   => $count,
                    'Excepted'                                               => $expected,
                    'numGroupsExpected['.($setting->numFighters - 1).']' => $numGroupsExpected[$setting->numFighters - 1].' / '.$setting->numArea,
                ]
            );
        }
        $this->assertTrue($count == $expected);
    }

    /**
     * @param $championship
     * @param $setting
     * @param $numFightsExpected
     * @param $methodName
     */
    protected function checkFightsNumber($championship, $setting, $numFightsExpected, $methodName)
    {
        $groupSize = $setting->hasPreliminary ? $setting->preliminaryGroupSize : 2;
        $count = $this->getFightsCount($championship);

        if ((int) ($setting->numFighters / $setting->numArea) <= 1
            || $setting->numFighters / ($groupSize * $setting->numArea) < 1) {
            $this->assertTrue($count == 0);

            return;
        }

        if ($count != $numFightsExpected) {
            dd(['Method'         => $methodName,
                'NumCompetitors' => $setting->numFighters,
                'NumArea'        => $setting->numArea,
                'Real'           => $count,
                'isTeam'         => $setting->isTeam,
                'Excepted'       => $numFightsExpected,
            ]);
        }
        $this->assertTrue($count == $numFightsExpected);
    }

    /**
     * @param $championship
     * @param $area
     *
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
     * @param $numFighters
     * @param $team
     *
     * @return stdClass
     */
    protected function createSetting($numArea, $numFighters, $team, $hasPreliminary, $preliminaryGroupSize): stdClass
    {
        $setting = new stdClass();
        $setting->numArea = $numArea;
        $setting->numFighters = $numFighters;
        $setting->preliminaryGroupSize = $preliminaryGroupSize;
        $setting->hasPlayOff = false;
        $setting->hasPreliminary = $hasPreliminary;
        $setting->isTeam = $team;

        return $setting;
    }

    public function initialSeed()
    {
        factory(Venue::class, 5)->create();

        Category::create(['name' => 'categories.junior', 'gender' => 'X', 'isTeam' => 0, 'ageCategory' => 5, 'ageMin' => '13', 'ageMax' => '15', 'gradeCategory' => 0]);
        Category::create(['name' => 'categories.junior_team', 'gender' => 'X', 'isTeam' => 1, 'ageCategory' => 5, 'ageMin' => '13', 'ageMax' => '15', 'gradeCategory' => 0]);
        Category::create(['name' => 'categories.men_single', 'gender' => 'M', 'isTeam' => 0, 'ageCategory' => 5, 'ageMin' => '18']);
        Category::create(['name' => 'categories.men_team', 'gender' => 'M', 'isTeam' => 1, 'ageCategory' => 5, 'ageMin' => '18']);
        Category::create(['name' => 'categories.ladies_single', 'gender' => 'F', 'isTeam' => 0, 'ageCategory' => 5, 'ageMin' => '18']);
        Category::create(['name' => 'categories.ladies_team', 'gender' => 'F', 'isTeam' => 1, 'ageCategory' => 5, 'ageMin' => '18']);
        Category::create(['name' => 'categories.master', 'gender' => 'F', 'isTeam' => 0, 'ageCategory' => 5, 'ageMin' => '50', 'gradeMin' => '8']); // 8 = Shodan

        $venues = Venue::all()->pluck('id')->toArray();

        $faker = Factory::create();
        $dateIni = $faker->dateTimeBetween('now', '+2 weeks')->format('Y-m-d');
        $user = factory(User::class)->create(['name' => 'user']);
        Tournament::create([
            'id'                => 1,
            'slug'              => md5(uniqid(rand(), true)),
            'user_id'           => $user->id,
            'name'              => 'Test Tournament',
            'dateIni'           => $dateIni,
            'dateFin'           => $dateIni,
            'registerDateLimit' => $dateIni,
            'sport'             => 1,
            'type'              => 0,
            'level_id'          => 7,
            'venue_id'          => $faker->randomElement($venues),

        ]);

        factory(Championship::class)->create(['tournament_id' => 1, 'category_id' => 1]);
        factory(Championship::class)->create(['tournament_id' => 1, 'category_id' => 2]);

        // COMPETITORS

        $championship = Championship::where('tournament_id', 1)->first();

        $users[] = factory(User::class)->create(['name' => 't1']);
        $users[] = factory(User::class)->create(['name' => 't2']);
        $users[] = factory(User::class)->create(['name' => 't3']);
        $users[] = factory(User::class)->create(['name' => 't4']);
        $users[] = factory(User::class)->create(['name' => 't5']);

        foreach ($users as $user) {
            factory(Competitor::class)->create([
                'championship_id' => $championship->id,
                'user_id'         => $user->id,
                'confirmed'       => 1,
            ]);
        }
    }
}
