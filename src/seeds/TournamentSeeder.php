<?php

use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;
use Xoco70\LaravelTournaments\Championship;
use Xoco70\LaravelTournaments\Tournament;
use Xoco70\LaravelTournaments\Venue;

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
        for ($i = 0; $i < 10; $i++) {
            try {
                factory(Championship::class)->create();
            } catch (QueryException $e) {
            } catch (PDOException $e) {

            }
        }
    }
}
