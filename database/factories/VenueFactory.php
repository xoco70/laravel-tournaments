<?php

use Xoco70\LaravelTournaments\Models\Venue;

$factory->define(Venue::class, function (Faker\Generator $faker) {
    return [
        'venue_name' => $faker->colorName,
        'address'    => $faker->streetName,
        'details'    => $faker->streetName,
        'city'       => $faker->city,
        'CP'         => $faker->postcode,
        'state'      => $faker->colorName,
        'latitude'   => $faker->latitude,
        'longitude'  => $faker->longitude,
    ];
});
