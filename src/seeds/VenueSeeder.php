<?php

use App\Venue;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Venues seeding!');
        DB::table('venue')->truncate();
        factory(Venue::class, 5)->create();


    }
}
