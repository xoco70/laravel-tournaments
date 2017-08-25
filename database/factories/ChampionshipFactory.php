<?php


use Xoco70\LaravelTournaments\Models\Category as Cat;
use Xoco70\LaravelTournaments\Models\Championship;
use Xoco70\LaravelTournaments\Models\Tournament;

$factory->define(Championship::class, function (Faker\Generator $faker) {
    $tournaments = Tournament::all()->pluck('id')->toArray();
    $categories = Cat::all()->pluck('id')->toArray();

    return [
        'tournament_id' => $faker->randomElement($tournaments),
        'category_id'   => $faker->randomElement($categories),
    ];
});
