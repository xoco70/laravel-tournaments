<?php


$factory->define(\Illuminate\Foundation\Auth\User::class, function (Faker\Generator $faker) {
    $email = $faker->email;

    return [
        'name'     => $faker->name,
        'email'    => $email,
        'slug'     => $faker->slug,
        'password' => bcrypt(str_random(10)),
    ];
});
