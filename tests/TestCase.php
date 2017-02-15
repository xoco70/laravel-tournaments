<?php

namespace Xoco70\KendoTournaments\Tests;


use Illuminate\Foundation\Auth\User;
use Orchestra\Database\ConsoleServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Xoco70\KendoTournaments\TournamentsServiceProvider;

abstract class TestCase extends BaseTestCase
{

    const DB_NAME = "plugin";
    const DB_USERNAME = 'root';
    const DB_PASSWORD = '';

    protected $root;

    protected function getPackageProviders($app)
    {
        return [TournamentsServiceProvider::class,
            ConsoleServiceProvider::class];
    }


    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->root = new \Illuminate\Foundation\Auth\User;
        $this->makeSureDatabaseExists();
        parent::setUp();
        $this->migrateAndSeed();
        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:rollback');
        });


        $this->withFactories(__DIR__ . '/../database/factories');

    }

    private function makeSureDatabaseExists()
    {
        $this->runQuery('CREATE DATABASE IF NOT EXISTS ' . static::DB_NAME);
    }

    /**
     * @param \Illuminate\Foundation\Auth\User $newUser
     */

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', [
            'driver'   => 'mysql',
            'host' => '127.0.0.1',
            'database' => static::DB_NAME,
            'username' => static::DB_USERNAME,
            'password' => static::DB_PASSWORD,
            'prefix'   => 'ken_',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'strict' => false,
        ]);
    }


    private function migrateAndSeed()
    {
//        $this->artisan('migrate', [
//            '--realpath' => realpath(__DIR__ . '/../database/migrations'),
//        ]);
//        $this->artisan('db:seed');

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

}
