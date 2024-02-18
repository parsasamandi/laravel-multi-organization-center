<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralInfoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('general_info', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->text('bank_statement')->nullable();
			$table->float('bank_balance', 10, 0);
			$table->char('date', 120);
			$table->integer('user_id')->unique('user_id_UNIQUE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('general_info');
	}

}
