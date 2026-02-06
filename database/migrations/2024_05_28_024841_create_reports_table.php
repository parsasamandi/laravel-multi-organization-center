<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reports', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->float('expenses', 10, 0);
			$table->string('range', 256);
			$table->text('receipt');
			$table->text('description')->nullable();
			$table->integer('type')->comment('0 = Employee Income Expenses;
1 = Education Expenses;
2 = Healthcare Expenses;');
			$table->integer('center_id')->index('center_id_idx');
			$table->integer('general_info_id')->index('general_info_idx');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('reports');
	}

}
