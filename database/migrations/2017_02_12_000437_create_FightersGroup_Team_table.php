<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Xoco70\LaravelTournaments\DBHelpers;

class CreateFightersGroupTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fighters_group_team', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('team_id')->unsigned()->nullable()->index();
            $table->integer('fighters_group_id')->unsigned()->index();
            $table->integer('order')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('team_id')
                ->references('id')
                ->on('team')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('fighters_group_id')
                ->references('id')
                ->on('fighters_groups')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unique(['team_id', 'fighters_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DBHelpers::setFKCheckOff();
        Schema::dropIfExists('fighters_group_team');
        DBHelpers::setFKCheckOn();
    }
}
