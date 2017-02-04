<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateTournamentLevelTable extends Migration {

	public function up()
	{
		Schema::create('tournamentLevel', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->engine = 'InnoDB';



		});
	}

	public function down()
	{
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');
		Schema::dropIfExists('tournament');
		Schema::dropIfExists('tournamentLevel');
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');
	}
}