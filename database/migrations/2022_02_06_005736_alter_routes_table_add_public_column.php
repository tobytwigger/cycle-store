<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRoutesTableAddPublicColumn extends Migration
{
    /**
     * Run the migrations.
     *
     */
    public function up()
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->boolean('public')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down()
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropColumn('public');
        });
    }
}
