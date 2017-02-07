<?php

use Illuminate\Database\Seeder;
use Xoco70\LaravelTournaments\User;

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
