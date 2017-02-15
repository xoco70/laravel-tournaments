<?php

use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Seeder;

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
        User::truncate();
//        factory(User::class, 4)->create();
    }
}
