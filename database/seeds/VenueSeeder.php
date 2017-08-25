<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Xoco70\LaravelTournaments\Models\Venue;

class VenueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Venues seeding!');
        DB::table('venue')->truncate();
        factory(Venue::class, 5)->create();
    }
}
