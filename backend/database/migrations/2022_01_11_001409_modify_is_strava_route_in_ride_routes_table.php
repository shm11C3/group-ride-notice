<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyIsStravaRouteInRideRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ride_routes', function (Blueprint $table) {
            $table->dropColumn('is_strava_route');
            $table->unsignedBigInteger('strava_route_id')->nullable();
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
            $table->dropColumn('strava_route_id');
            $table->boolean('is_strava_route')->default(false);
        });
    }
}
