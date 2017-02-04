<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAssociationTable extends Migration {

	public function up()
	{
		Schema::create('association', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->integer('federation_id')->nullable()->unsigned();
			$table->integer('president_id')->nullable()->unsigned();
			$table->string('state')->nullable();
			$table->string('address')->nullable();
			$table->string('phone')->nullable();
            $table->string('website')->nullable();
            
//			$table->integer('stateId')->unsigned();
			$table->timestamps();
			$table->softDeletes();
			$table->engine = 'InnoDB';

			$table->unique(['name','deleted_at'], 'association_name_unique');
            $table->unique(['president_id'], 'association_president_unique');

			$table->foreign('president_id')
				->references('id')
				->on('users')
				->onUpdate('cascade')
				->onDelete('cascade');

			$table->foreign('federation_id')
				->references('id')
				->on('federation')
				->onUpdate('cascade')
				->onDelete('cascade');

		});
	}

	public function down()
	{
		Schema::drop('association');
	}
}