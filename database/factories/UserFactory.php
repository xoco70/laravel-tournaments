<?php


$factory->define(\Illuminate\Foundation\Auth\User::class, function (Faker\Generator $faker) {
    $email = $faker->email;

    return [
        'name'      => $faker->name,
        'email'     => $email,
        'password'  => app('hash')->make(str_random(10)),
        'firstname' => $faker->firstName,
        'lastname'  => $faker->lastName,
    ];
});
