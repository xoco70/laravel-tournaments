<?php

use Xoco70\LaravelTournaments\Models\Team;

$factory->define(Team::class, function (Faker\Generator $faker) {
    return [
        'name'            => $faker->name,
        'championship_id' => 2,
    ];
});
