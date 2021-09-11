<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rides', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->uuid('host_user_uuid');
            $table->uuid('meeting_places_uuid');
            $table->uuid('ride_routes_uuid');
            $table->string('name', 32);
            $table->dateTime('time_appoint');
            $table->unsignedTinyInteger('intensity');
            $table->string('comment', 1024);
            $table->unsignedTinyInteger('publish_status')->default(0);
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
        Schema::dropIfExists('rides');
    }
}
