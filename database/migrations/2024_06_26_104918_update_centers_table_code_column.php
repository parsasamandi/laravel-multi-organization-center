<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateCentersTableCodeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('centers', function (Blueprint $table) {
            // Explicitly cast the existing string values to integers
            DB::statement('ALTER TABLE centers ALTER COLUMN code TYPE INTEGER USING (code::integer)');
            
            // Make the code column nullable
            $table->integer('code')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('centers', function (Blueprint $table) {
            // Explicitly cast the existing integer values back to strings
            DB::statement('ALTER TABLE centers ALTER COLUMN code TYPE VARCHAR USING (code::text)');
            
            // Revert the code column to its original state
            $table->string('code')->nullable()->change();
        });
    }
}

