<?php

use Illuminate\Database\Migrations\Migration;
use \Xoco70\KendoTournaments\DBHelpers;
class AddUsersFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function($table) {
            $table->string('firstname');
            $table->string('lastname');

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
        Schema::table('users', function($table) {
            $table->dropColumn('firstname');
            $table->dropColumn('lastname');
        });
        DBHelpers::setFKCheckOn();
    }
}
