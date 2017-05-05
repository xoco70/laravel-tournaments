<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kalnoy\Nestedset\NestedSet;

class CreateFightersGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fighters_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('short_id')->unsigned()->nullable();
            $table->integer('championship_id')->unsigned()->index();
            $table->tinyInteger('round')->default(0); // Eliminitory, 1/8, 1/4, etc.
            $table->tinyInteger('area');
            $table->tinyInteger('order');
            NestedSet::columns($table);

            $table->timestamps();
            $table->engine = 'InnoDB';

            $table->foreign('championship_id')
                ->references('id')
                ->onUpdate('cascade')
                ->on('championship')
                ->onDelete('cascade');
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
        Schema::dropIfExists('fighters_groups');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
