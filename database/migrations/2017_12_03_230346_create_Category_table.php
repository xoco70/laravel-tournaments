<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Xoco70\LaravelTournaments\DBHelpers;

class CreateCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('gender')->nullable();
            $table->integer('isTeam')->unsigned()->default(0);
            $table->integer('ageCategory')->unsigned()->default(0); // 0 = none, 1 = child, 2= teenager, 3 = adult, 4 = master
            $table->integer('ageMin')->unsigned()->default(0);
            $table->integer('ageMax')->unsigned()->default(0);
            $table->integer('gradeCategory')->unsigned()->default(0);
            $table->integer('gradeMin')->unsigned()->default(0);
            $table->integer('gradeMax')->unsigned()->default(0);
            $table->unique(['name', 'gender', 'isTeam', 'ageCategory', 'ageMin', 'ageMax', 'gradeCategory', 'gradeMin', 'gradeMax'], 'category_fields_unique');
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        Schema::create('championship', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tournament_id')->unsigned()->index();
            $table->integer('category_id')->unsigned()->index();
            $table->unique(['tournament_id', 'category_id']);

            $table->foreign('tournament_id')
                ->references('id')
                ->on('tournament')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('category_id')
                ->references('id')
                ->on('category')
                ->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
            $table->engine = 'InnoDB';
        });

        Schema::create('competitor', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('short_id')->unsigned()->nullable();
            $table->integer('championship_id')->unsigned()->index();
            $table->foreign('championship_id')
                ->references('id')
                ->on('championship')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unique(['championship_id', 'short_id']);
            $table->unique(['championship_id', 'user_id']);

            $table->boolean('confirmed');

            $table->timestamps();
            $table->softDeletes();
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
        DBHelpers::setFKCheckOff();
        Schema::dropIfExists('competitor');
        Schema::dropIfExists('championship');
        Schema::dropIfExists('category');
        DBHelpers::setFKCheckOn();
    }
}
