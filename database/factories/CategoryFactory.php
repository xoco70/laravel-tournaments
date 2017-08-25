<?php


use Xoco70\LaravelTournaments\Models\Category as Cat;

$factory->define(Cat::class, function (Faker\Generator $faker) {
    $name = ['categories.junior',
        'categories.junior_team',
        'categories.men_single',
        'categories.men_team',
        'categories.ladies_single',
        'categories.ladies_team',
        'categories.master',
    ];
    $gender = ['M', 'F', 'X'];

    return [
        'name'          => $faker->randomElement($name),
        'gender'        => $faker->randomElement($gender),
        'isTeam'        => $faker->numberBetween(0, 1),
        'ageCategory'   => $faker->numberBetween(0, 5),
        'ageMin'        => $faker->numberBetween(1, 90),
        'ageMax'        => $faker->numberBetween(1, 90),
        'gradeCategory' => $faker->numberBetween(2, 16),
        'gradeMin'      => $faker->numberBetween(2, 16),
        'gradeMax'      => $faker->numberBetween(2, 16),
    ];
});
