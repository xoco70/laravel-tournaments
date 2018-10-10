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
        Schema::table(config('user.table'), function (Blueprint $table) {
            if (!Schema::hasColumn('name')) {
                $table->string('name')->default('name');
            }
            if (!Schema::hasColumn('firstname')) {
                $table->string('firstname')->default('firstname');
            }
            if (!Schema::hasColumn('lastname')) {
                $table->string('lastname')->default('lastname');
            }
            if (!Schema::hasColumn('email')) {
                $table->string('email')->unique();
            }

            if (!Schema::hasColumn('password')) {
                $table->string('password', 60);
            }
        });

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
