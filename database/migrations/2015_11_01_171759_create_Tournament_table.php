<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateTournamentTable extends Migration
{
    public function up()
    {
        Schema::create('tournament', function (Blueprint $table) {
            $table->increments('id');
            $table->Integer('user_id')->unsigned()->index();
            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');

            $table->string('name');
            $table->string('slug')->unique();
            $table->date('dateIni');
            $table->date('dateFin');
            $table->date('registerDateLimit');
            $table->integer('sport')->unsigned()->default(1); // Default is Kendo for now
            $table->string('promoter')->nullable();
            $table->string('host_organization')->nullable();
            $table->string('technical_assistance')->nullable();
            $table->integer('rule_id')->default(1);
            $table->tinyInteger('type')->default(1); // 1= local, 2= state, 3= national, 4=continent, 5=world
            $table->integer('venue_id')->nullable()->unsigned();
            $table->integer('level_id')->unsigned()->default(1);

            $table->foreign('venue_id')
                ->references('id')
                ->on('venue');

            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
        });
    }

    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('tournament');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
