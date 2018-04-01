<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Xoco70\LaravelTournaments\DBHelpers;

class CreateTeamTable extends Migration
{
    public function up()
    {
        Schema::create('team', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('short_id')->unsigned()->nullable();
            $table->string('name');
            $table->integer('championship_id')->unsigned()->index(); // A checar
            $table->string('picture')->nullable();
            $table->string('entity_type')->nullable(); // Club, Assoc, Fed
            $table->integer('entity_id')->unsigned()->nullable()->index();
            $table->timestamps();

            $table->foreign('championship_id')
                ->references('id')
                ->on('championship')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unique(['championship_id', 'name']);
        });
    }

    public function down()
    {
        DBHelpers::setFKCheckOff();
        Schema::drop('team');
        DBHelpers::setFKCheckOn();
    }
}
