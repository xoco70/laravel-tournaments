<?php


use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\ChampionshipSettings;

$factory->define(ChampionshipSettings::class, function (Faker\Generator $faker) use ($factory) {
    $tcs = Championship::all()->pluck('id')->toArray();

    return [
        'championship_id'      => $faker->randomElement($tcs),
        'treeType'             =>  $faker->numberBetween(0, 1),
        'alias'                => $faker->word,
        'teamSize'             => $faker->numberBetween(0, 6),
        'fightingAreas'        => $faker->numberBetween(0, 4),
        'fightDuration'        => '03:00',
        'hasPreliminary'       => $faker->boolean(),
        'preliminaryWinner'    => $faker->numberBetween(1, 2),
        'hasEncho'             => $faker->boolean(),
        'enchoQty'             => $faker->numberBetween(0, 4),
        'enchoDuration'        => '01:00',
        'hasHantei'            => $faker->boolean(),
        'cost'                 => $faker->numberBetween(0, 100),
        'preliminaryGroupSize' => $faker->numberBetween(0, 10),
        'preliminaryDuration'  => $faker->numberBetween(0, 10),
        'seedQuantity'         => $faker->numberBetween(0, 4),
        'hanteiLimit'          => $faker->numberBetween(0, 10), // 1/2 Finals
        'enchoGoldPoint'       => $faker->numberBetween(0, 10), // Step where Encho has no more time limit
        'limitByEntity'        => $faker->numberBetween(0, 10),

    ];
});
