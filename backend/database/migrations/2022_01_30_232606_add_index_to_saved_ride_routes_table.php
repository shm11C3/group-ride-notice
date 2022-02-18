<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToSavedRideRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('saved_ride_routes', function (Blueprint $table) {
            $table->index('user_uuid');
            $table->index('route_uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('saved_ride_routes', function (Blueprint $table) {
            $table->dropIndex(['user_uuid']);
            $table->dropIndex(['route_uuid']);
        });
    }
}
