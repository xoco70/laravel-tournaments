<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class AlterLtUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable(config('laravel-tournaments.user.table'))) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('firstname')->default('firstname');
                $table->string('lastname')->default('lastname');
                $table->string('email')->unique();
                $table->string('password', 60);
                $table->timestamps();
            });
        } else {
            Schema::table(config('laravel-tournaments.user.table'), function (Blueprint $table) {
                $faker = Faker\Factory::create();

                if (!Schema::hasColumn(config('laravel-tournaments.user.table'), 'name')) {
                    $table->string('name')->default('name');
                }
                if (!Schema::hasColumn(config('laravel-tournaments.user.table'), 'firstname')) {
                    $table->string('firstname')->default($faker->firstname);
                }
                if (!Schema::hasColumn(config('laravel-tournaments.user.table'), 'lastname')) {
                    $table->string('lastname')->default($faker->lastname);
                }
                if (!Schema::hasColumn(config('laravel-tournaments.user.table'), 'email')) {
                    $table->string('email')->unique();
                }

                if (!Schema::hasColumn(config('laravel-tournaments.user.table'), 'password')) {
                    $table->string('password', 60);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
