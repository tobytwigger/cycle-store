<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('file_id')->nullable();
            $table->text('linked_to')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down()
    {
        Schema::dropIfExists('activities');
    }
}
