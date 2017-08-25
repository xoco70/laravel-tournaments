<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Xoco70\LaravelTournaments\DBHelpers;

class LaravelTournamentSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        $this->command->info('Seeding...');

        DBHelpers::setFKCheckOff();

        DB::table('competitor')->truncate();
        DB::table('tournament')->truncate();
        DB::table('category')->truncate();
        DB::table('users')->truncate();
        DB::table('venue')->truncate();

        $this->call(VenueSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(TournamentSeeder::class);
        $this->call(CompetitorSeeder::class);

        $this->command->info('All tables seeded!');
        DBHelpers::setFKCheckOn();
        Model::reguard();
    }
}
