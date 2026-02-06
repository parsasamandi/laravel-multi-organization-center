<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCentersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('centers', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('code')->unique('code_unique');
			$table->string('name')->unique('name_unique');
			$table->string('phone_number', 20)->unique('phone_number_unique');
			$table->string('email')->nullable();
			$table->string('password');
			$table->integer('type')->default(0)->comment('0 = Center; 1 = Golestan Team');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('centers');
	}

}
