<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToReportTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('report', function(Blueprint $table)
		{
			$table->foreign('center_id', 'center_id')->references('id')->on('center_name')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('general_info_id', 'general_id')->references('id')->on('general_info')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('report', function(Blueprint $table)
		{
			$table->dropForeign('center_id');
			$table->dropForeign('general_id');
		});
	}

}
