<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class CreateLtUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable(Config::get('laravel-tournaments.user_table'))) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('firstname')->default('firstname');
                $table->string('lastname')->default('lastname');
                $table->string('email')->unique();
                $table->string('password', 60);
                $table->rememberToken();
                $table->timestamps();
            });
        } else {
            Schema::table(Config::get('laravel-tournaments.user_table'), function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('firstname')->default('firstname');
                $table->string('lastname')->default('lastname');
                $table->string('email')->unique();
                $table->string('password', 60);
                $table->rememberToken();
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
