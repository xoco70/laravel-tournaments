<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoundCompetitorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('round_competitor', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('competitor_id')->unsigned()->nullable()->index();
            $table->integer('round_id')->unsigned()->index(); // A checar
            $table->timestamps();


            $table->foreign('competitor_id')
                ->references('id')
                ->on('competitor')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('round_id')
                ->references('id')
                ->on('round')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unique(['competitor_id', 'round_id']);
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
        Schema::dropIfExists('round_competitor');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }
}
