<?php

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Xoco70\LaravelTournaments\Models\Championship;
use Xoco70\LaravelTournaments\Models\Competitor;
use Xoco70\LaravelTournaments\Models\User;


class CompetitorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Competitors seeding!');
        $faker = Faker::create();

        $championship = Championship::where('tournament_id', 1)->first();


        $users = factory(User::class, $faker->numberBetween(15, 50))->create(
            ['password' => bcrypt('111111')]);

        foreach ($users as $user) {
            factory(Competitor::class)->create([
                'championship_id' => $championship->id,
                'user_id' => $user->id,
                'confirmed' => 1,
            ]);
        }

    }
}
