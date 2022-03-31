<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('waypoints', function (Blueprint $table) {
            $table->id();
            $table->point('points')->nullable();
//            'latitude' => $this->getLatitude(),
//            'longitude' => $this->getLongitude(),
            $table->float('elevation')->nullable();
            $table->dateTime('time')->nullable();
            $table->float('cadence')->nullable();
            $table->float('temperature')->nullable();
            $table->float('heart_rate')->nullable();
            $table->float('speed')->nullable();
            $table->float('grade')->nullable();
            $table->float('battery')->nullable();
            $table->float('calories')->nullable();
            $table->float('cumulative_distance')->nullable();
            $table->unsignedBigInteger('stats_id');
            $table->timestamps();
        });

        Schema::table('waypoints', function(Blueprint $table) {
            $table->index(['stats_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('waypoints');
    }
};
