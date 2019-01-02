<?php


$factory->define(config('laravel-tournaments.user.model'), function (Faker\Generator $faker) {
    return [
        'name'               => $faker->name,
        'email'              => $faker->unique()->safeEmail,
        'password'           => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'firstname'          => $faker->firstname,
        'lastname'           => $faker->lastName,
    ];
});
