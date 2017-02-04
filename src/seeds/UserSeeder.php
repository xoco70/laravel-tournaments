<?php

use App\Association;
use App\Club;
use App\Country;
use App\Federation;
use App\Grade;
use App\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Webpatser\Countries\Countries;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Users seeding!');
        factory(User::class, 10)->create();
    }
}
