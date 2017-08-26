<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Xoco70\LaravelTournaments\DBHelpers;

class AddUsersFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('firstname')->default('firstname');
            $table->string('lastname')->default('lastname');
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
        if (Schema::hasColumn('users', 'firstname')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('firstname');
            });
        }
        if (Schema::hasColumn('users', 'lastname')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('lastname');
            });
        }
        DBHelpers::setFKCheckOn();
    }
}
