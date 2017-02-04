<?php

use App\Association;
use App\Championship;
use App\Club;
use App\Competitor;
use App\Country;
use App\Federation;
use App\Grade;
use App\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Webpatser\Countries\Countries;

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
                ['country_id' => 484,
                    'password' => bcrypt('111111'),
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
