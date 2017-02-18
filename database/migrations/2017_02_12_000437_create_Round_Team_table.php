<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoundTeamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('round_team', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('team_id')->unsigned()->nullable()->index();
            $table->integer('round_id')->unsigned()->index(); // A checar
            $table->timestamps();


            $table->foreign('team_id')
                ->references('id')
                ->on('team')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('round_id')
                ->references('id')
                ->on('round')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unique(['team_id', 'round_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('round_team');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }
}
