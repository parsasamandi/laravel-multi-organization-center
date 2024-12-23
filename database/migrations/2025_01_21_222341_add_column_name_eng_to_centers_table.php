<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnNameEngToCentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('centers', function (Blueprint $table) {
            // Add the 'name_en' column, ensuring compatibility with PostgreSQL
            if (!Schema::hasColumn('centers', 'name_en')) {
                $table->string('name_en')->nullable()->after('name');
            }
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
            // Drop the 'name_en' column if it exists
            if (Schema::hasColumn('centers', 'name_en')) {
                $table->dropColumn('name_en');
            }
        });
    }
}
