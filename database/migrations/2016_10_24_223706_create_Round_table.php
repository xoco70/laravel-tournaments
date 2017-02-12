<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoundTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('round', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('championship_id')->unsigned()->index();
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
        Schema::dropIfExists('round');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
