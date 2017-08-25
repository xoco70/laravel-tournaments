<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaravelTournamentSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        $this->command->info('Seeding...');

        switch (DB::getDriverName()) {
            case 'mysql':
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                break;
            case 'sqlite':
                DB::statement('PRAGMA foreign_keys = OFF');
                break;
        }


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

        switch (DB::getDriverName()) {
            case 'mysql':
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
                break;
            case 'sqlite':
                DB::statement('PRAGMA foreign_keys = ON');
                break;
        }


        Model::reguard();
    }
}
