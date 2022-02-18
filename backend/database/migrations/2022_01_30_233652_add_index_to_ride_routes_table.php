<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToRideRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ride_routes', function (Blueprint $table) {
            $table->index('user_uuid');
            $table->index('strava_route_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ride_routes', function (Blueprint $table) {
            $table->dropIndex(['user_uuid']);
            $table->dropIndex(['route_uuid']);
        });
    }
}
