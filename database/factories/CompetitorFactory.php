<?php

use App\User;
use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\Competitor;

$factory->define(Competitor::class, function (Faker\Generator $faker) {
    $tcs = Championship::all()->pluck('id')->toArray();
    $users = User::all()->pluck('id')->toArray();

    return [
        'championship_id' => $faker->randomElement($tcs),
        'user_id' => $faker->randomElement($users),
        'confirmed' => $faker->numberBetween(0, 1),
    ];
});
