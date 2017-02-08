<?php

use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;
use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\Venue;
use Xoco70\KendoTournaments\Models\Tournament;

class TournamentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Tournament seeding!');
        $venues = Venue::all()->pluck('id')->toArray();

        // Tournament creation
        Tournament::truncate();
        $faker = Faker\Factory::create();
        $dateIni = $faker->dateTimeBetween('now', '+2 weeks')->format('Y-m-d');
        Tournament::create([
            'id' => 1,
            'user_id' => 1,
            'name' => "Test Tournament",
            'dateIni' =>  $dateIni,
            'dateFin' =>  $dateIni,
            'registerDateLimit' =>  $dateIni,
            'sport' => 1,
            'type' => 0,
            'level_id' => 7,
            'venue_id' => $faker->randomElement($venues),


        ]);

        Championship::truncate();
        factory(Championship::class)->create(['tournament_id' => 1]);
    }
}
