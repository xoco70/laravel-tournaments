<?php

use Illuminate\Database\Seeder;
use Xoco70\LaravelTournaments\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::truncate();
        // Presets

        Category::create(['name' => 'categories.junior', 'gender' => 'X', 'isTeam' => 0, 'ageCategory' => 5, 'ageMin' => '13', 'ageMax' => '15', 'gradeCategory' => 0]);
        Category::create(['name' => 'categories.junior_team', 'gender' => 'X', 'isTeam' => 1, 'ageCategory' => 5, 'ageMin' => '13', 'ageMax' => '15', 'gradeCategory' => 0]);

        Category::create(['name' => 'categories.men_single', 'gender' => 'M', 'isTeam' => 0, 'ageCategory' => 5, 'ageMin' => '18']);
        Category::create(['name' => 'categories.men_team', 'gender' => 'M', 'isTeam' => 1, 'ageCategory' => 5, 'ageMin' => '18']);

        Category::create(['name' => 'categories.ladies_single', 'gender' => 'F', 'isTeam' => 0, 'ageCategory' => 5, 'ageMin' => '18']);
        Category::create(['name' => 'categories.ladies_team', 'gender' => 'F', 'isTeam' => 1, 'ageCategory' => 5, 'ageMin' => '18']);

        Category::create(['name' => 'categories.master', 'gender' => 'F', 'isTeam' => 0, 'ageCategory' => 5, 'ageMin' => '50', 'gradeMin' => '8']); // 8 = Shodan

        // Junior Team :  3 - 5
        // Junior Individual,

        // Senior Male Team : Team 5 - 7
        // Senior Female Team : Team 5 - 7

        // Senior Female Individual,
        // Senior Male Individual
    }
}
