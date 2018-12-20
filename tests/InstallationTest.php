<?php

namespace Xoco70\LaravelTournaments\Tests;

use Xoco70\LaravelTournaments\Models\ChampionshipSettings;
use Xoco70\LaravelTournaments\Models\FightersGroup;

class InstallationTest extends TestCase
{
    /** @test */
    public function it_installs()
    {
        exec('composer create-project plugin-test "'.env('LARAVEL_VERSION').'"');
        exec('cd plugin-test');
        exec('composer require "xoco70/laravel-tournaments:dev-master"');
        exec('php artisan vendor:publish --tag=laravel-tournaments --force');
        exec('touch database/database.sqlite');
        exec('php artisan migrate:fresh --database=sqlite --force');
        exec('composer dump-autoload');
        exec('php artisan db:seed --class=LaravelTournamentSeeder --database=sqlite --force');
//        self::assertTrue(true);
    }
}
