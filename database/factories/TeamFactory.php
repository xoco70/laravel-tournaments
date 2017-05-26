<?php


use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\Team;

$factory->define(Team::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'championship_id' => Championship::find(2),
    ];
});
