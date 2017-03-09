<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFightTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fight', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fighters_group_id')->unsigned()->index();
            $table->foreign('fighters_group_id')
                ->references('id')
                ->on('fighters_groups')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->integer('c1')->nullable()->unsigned()->index();

            $table->integer('c2')->nullable()->unsigned()->index();

            $table->tinyInteger('area');
            $table->tinyInteger('order');
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('fight');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
