<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFederationTable extends Migration {

	public function up()
	{
		Schema::create('federation', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name')->unique();
			$table->integer('president_id')->nullable()->unsigned();
			$table->string('email')->nullable();
			$table->string('address')->nullable();
			$table->string('phone')->nullable();
			$table->string('website')->nullable();

            // Direction, phone, contact

			$table->timestamps();
            $table->softDeletes();
			$table->engine = 'InnoDB';
            $table->unique(['president_id'], 'fed_president_unique');

			$table->foreign('president_id')
				->references('id')
				->on('users')
				->onUpdate('cascade')
				->onDelete('cascade');

		});
	}

	public function down()
	{
		Schema::drop('federation');
	}
}