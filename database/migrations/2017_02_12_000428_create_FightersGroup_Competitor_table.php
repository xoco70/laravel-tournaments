<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFightersGroupCompetitorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fighters_group_competitor', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('competitor_id')->unsigned()->nullable()->index();
            $table->integer('fighters_group_id')->unsigned()->index();
            $table->integer('order')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('competitor_id')
                ->references('id')
                ->on('competitor')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('fighters_group_id')
                ->references('id')
                ->on('fighters_groups')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unique(['competitor_id', 'fighters_group_id']);
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
        Schema::dropIfExists('fighters_group_competitor');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
