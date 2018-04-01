<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVenueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('venue', function (Blueprint $table) {
            $table->increments('id');
            $table->string('venue_name');
            $table->string('address')->nullable();
            $table->string('details')->nullable();
            $table->string('city')->nullable();
            $table->string('CP')->nullable();
            $table->string('state')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('venue');
    }
}
