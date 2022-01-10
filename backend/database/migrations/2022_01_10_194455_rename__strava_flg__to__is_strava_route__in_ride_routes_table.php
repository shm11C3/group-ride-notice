<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameStravaFlgToIsStravaRouteInRideRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ride_routes', function (Blueprint $table) {
            $table->renameColumn('strava_flg', 'is_strava_route'); // strava_flgのカラム名をわかりやすく変更
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
            $table->renameColumn('is_strava_route', 'strava_flg');
        });
    }
}
