<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralInfosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('general_infos', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->text('bank_statement_receipt')->nullable();
			$table->float('bank_balance', 10, 0);
			$table->integer('jalaliMonth');
			$table->integer('jalaliYear');
			$table->integer('center_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('general_infos');
	}

}
