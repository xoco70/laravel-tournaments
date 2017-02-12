<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFightTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fight', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('round_id')->unsigned()->index();
            $table->foreign('round_id')
                ->references('id')
                ->on('round')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->integer('c1')->nullable()->unsigned()->index();
            $table->foreign('c1')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('c2')->nullable()->unsigned()->index();
            $table->foreign('c2')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->tinyInteger("area");
            $table->tinyInteger("order");
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
