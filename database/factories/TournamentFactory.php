<?php

use Illuminate\Foundation\Auth\User;
use Xoco70\LaravelTournaments\Models\Tournament;
use Xoco70\LaravelTournaments\Models\Venue;

$factory->define(Tournament::class, function (Faker\Generator $faker) {
    $users = User::all()->pluck('id')->toArray();
    if (count($users) == 0) {
        $user = factory(\Illuminate\Foundation\Auth\User::class)->create();
        $users[] = $user->id;
    }

    $dateIni = $faker->dateTimeBetween('now', '+2 weeks')->format('Y-m-d');
    $venues = Venue::all()->pluck('id')->toArray();

    return [
        'user_id'           => $faker->randomElement($users),
        'slug'              => $faker->slug(2),
        'name'              => $faker->name,
        'dateIni'           => $dateIni,
        'dateFin'           => $dateIni,
        'registerDateLimit' => $dateIni,
        'sport'             => 1,
        'type'              => $faker->boolean(),
        'venue_id'          => $faker->randomElement($venues),
    ];
});
