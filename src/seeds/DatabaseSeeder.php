<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
        Model::unguard();

        $this->command->info('Seeding...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::table('users')->truncate();


        $this->call(VenueSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(TournamentSeeder::class);
        $this->call(CompetitorSeeder::class);

        $this->command->info('All tables seeded!');


        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        Model::reguard();
    }
}