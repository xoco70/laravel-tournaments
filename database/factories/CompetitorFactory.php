<?php

use Illuminate\Foundation\Auth\User;
use Xoco70\LaravelTournaments\Models\Championship;
use Xoco70\LaravelTournaments\Models\Competitor;

$factory->define(Competitor::class, function (Faker\Generator $faker) {
    $tcs = Championship::all()->pluck('id')->toArray();
    $users = User::all()->pluck('id')->toArray();
    $championshipId = $faker->randomElement($tcs);
    $championship = Championship::find($championshipId);
    $tournament = $championship->tournament;

    return [
        'championship_id' => $faker->randomElement($tcs),
        'user_id'         => $faker->randomElement($users),
        'confirmed'       => $faker->numberBetween(0, 1),
        'short_id'        => $tournament->competitors()->max('short_id') + 1,
    ];
});
