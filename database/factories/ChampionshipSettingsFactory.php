<?php


use Xoco70\LaravelTournaments\Models\Championship;
use Xoco70\LaravelTournaments\Models\ChampionshipSettings;

$factory->define(ChampionshipSettings::class, function (Faker\Generator $faker) use ($factory) {
    $tcs = Championship::all()->pluck('id')->toArray();

    $fightingAreas = [1,2,4,8];
    return [
        'championship_id'      => $faker->randomElement($tcs),
        'treeType'             => $faker->numberBetween(0, 1),
        'alias'                => $faker->word,
        'teamSize'             => $faker->numberBetween(0, 6),
        'fightingAreas'        => $faker->randomElement($fightingAreas),
        'hasPreliminary'       => $faker->boolean(),
        'preliminaryWinner'    => $faker->numberBetween(1, 2),
        'preliminaryGroupSize' => $faker->numberBetween(3, 5),
        'seedQuantity'         => $faker->numberBetween(0, 4),
    ];
});
