<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Xoco70\LaravelTournaments\DBHelpers;

class CreateCompetitorTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competitor_team', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('team_id')->unsigned()->nullable()->index();
            $table->integer('competitor_id')->unsigned()->index();
            $table->integer('order')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('team_id')
                ->references('id')
                ->on('team')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('competitor_id')
                ->references('id')
                ->on('competitor')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unique(['team_id', 'competitor_id']);
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
        Schema::dropIfExists('competitor_team');
        DBHelpers::setFKCheckOn();
    }
}
