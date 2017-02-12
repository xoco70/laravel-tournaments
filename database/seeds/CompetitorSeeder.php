<?php

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Xoco70\KendoTournaments\Models\Championship;
use Xoco70\KendoTournaments\Models\Competitor;
use App\User;


class CompetitorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Competitor::truncate();

    }
}
