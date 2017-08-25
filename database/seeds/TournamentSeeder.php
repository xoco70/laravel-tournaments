<?php

use Illuminate\Database\Seeder;
use Xoco70\LaravelTournaments\Models\Championship;
use Xoco70\LaravelTournaments\Models\Tournament;
use Xoco70\LaravelTournaments\Models\Venue;

class TournamentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $venues = Venue::all()->pluck('id')->toArray();

        Tournament::truncate();
        $faker = Faker\Factory::create();
        $dateIni = $faker->dateTimeBetween('now', '+2 weeks')->format('Y-m-d');

        Tournament::create([
            'id'                => 1,
            'slug'              => md5(uniqid(rand(), true)),
            'user_id'           => 1,
            'name'              => 'Test Tournament',
            'dateIni'           => $dateIni,
            'dateFin'           => $dateIni,
            'registerDateLimit' => $dateIni,
            'sport'             => 1,
            'type'              => 0,
            'level_id'          => 7,
            'venue_id'          => $faker->randomElement($venues),

        ]);

        Championship::truncate();
        factory(Championship::class)->create(['tournament_id' => 1, 'category_id' => 1]);
        factory(Championship::class)->create(['tournament_id' => 1, 'category_id' => 2]);
    }
}
