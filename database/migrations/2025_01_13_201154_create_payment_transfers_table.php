<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the 'payment_transfers' table if it exists
        if (Schema::hasTable('expense_transfers')) {
            Schema::drop('expense_transfers');
        }

        // Create the 'payment_transfers' table
        Schema::create('payment_transfers', function (Blueprint $table) {
            $table->increments('id'); // Primary key
            $table->date('date')->comment('Date of the transfer');
            $table->integer('center_id')->comment('Foreign key referencing the center');
            $table->decimal('total_cad', 15, 2)->comment('Total transfer payment in CAD');
            $table->decimal('total_rial', 15, 2)->comment('Total transfer payment in RIAL');
            $table->decimal('cad_to_usd_rate', 10, 4)->comment('Exchange rate: USD to CAD');
            $table->decimal('salary', 15, 2)->nullable()->comment('Salary-related payment');
            $table->decimal('education', 15, 2)->nullable()->comment('Education-related payment');
            $table->decimal('food', 15, 2)->nullable()->comment('Food-related payment');
            $table->decimal('outfit', 15, 2)->nullable()->comment('Outfit-related payment');
            $table->decimal('misc', 15, 2)->nullable()->comment('Miscellaneous payment');
            $table->text('misc_desc')->nullable()->comment('Description about the miscellaneous expenses');
            $table->foreign('center_id')->references('id')->on('centers')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the 'payment_transfers' table
        Schema::dropIfExists('payment_transfers');
    }
}
