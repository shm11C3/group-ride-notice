<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStravaUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('strava_users', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_uuid')->unique();
            $table->string('strava_id')->unique();
            $table->integer('expires_at');
            $table->string('refresh_token');
            $table->string('access_token');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('strava_users');
    }
}
