<?php

use App\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Xoco70\LaravelTournaments\Championship;
use Xoco70\LaravelTournaments\Competitor;


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

        $championships = Championship::where('tournament_id', 1)->get();

        foreach ($championships as $championship) {
            $users = factory(User::class, $faker->numberBetween(15, 50))->create(
                [   'password' => bcrypt('111111'),
                    'verified' => 1]);
            foreach ($users as $user) {
                factory(Competitor::class)->create([
                    'championship_id' => $championship->id,
                    'user_id' => $user->id,
                    'confirmed' => 1,
                ]);
            }
        }
    }
}
