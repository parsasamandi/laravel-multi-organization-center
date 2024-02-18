<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('report', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->float('expenses', 10, 0);
			$table->float('range', 10, 0);
			$table->float('receipt', 10, 0);
			$table->text('description')->nullable();
			$table->integer('type');
			$table->integer('center_id')->unique('center_id_UNIQUE');
			$table->integer('general_info_id')->unique('general_info_id_UNIQUE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('report');
	}

}
