<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavedMeetingPlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saved_meeting_places', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_uuid');
            $table->uuid('meeting_place_uuid');
            $table->unsignedTinyInteger('meeting_place_category_id');
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
        Schema::dropIfExists('saved_meeting_places');
    }
}
