<?php

use App\User;
use Xoco70\KendoTournaments\Models\Venue;
use Xoco70\KendoTournaments\Models\Tournament;

$factory->define(Tournament::class, function (Faker\Generator $faker) {
    $users = User::all()->pluck('id')->toArray();

    $dateIni = $faker->dateTimeBetween('now', '+2 weeks')->format('Y-m-d');
    $venues = Venue::all()->pluck('id')->toArray();
    return [
        'user_id' => $faker->randomElement($users),
        'name' => $faker->name,
        'dateIni' => $dateIni,
        'dateFin' => $dateIni,
        'registerDateLimit' => $dateIni,
        'sport' => 1,
        'type' => $faker->boolean(),
        'venue_id' => $faker->randomElement($venues),
    ];
});