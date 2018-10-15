<?php


use Xoco70\LaravelTournaments\Models\Tournament;
use Xoco70\LaravelTournaments\Models\Venue;

$factory->define(Tournament::class, function (Faker\Generator $faker) {
    $dateIni = $faker->dateTimeBetween('now', '+2 weeks')->format('Y-m-d');
    $venues = Venue::all()->pluck('id')->toArray();

    return [
        'user_id'           => factory(\Illuminate\Foundation\Auth\User::class)->create()->id,
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
