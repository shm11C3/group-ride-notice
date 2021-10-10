<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNullableToUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('user_intro', 128)->default('')->nullable()->change();
            $table->string('user_url', 128)->default('')->nullable()->change();
            $table->string('fb_username', 64)->default('')->nullable()->change();
            $table->string('tw_username', 16)->default('')->nullable()->change();
            $table->string('ig_username', 32)->default('')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('user_intro', 128)->default('')->nullable()->change();
            $table->string('user_url', 128)->default('')->nullable()->change();
            $table->string('fb_username', 64)->default('')->nullable()->change();
            $table->string('tw_username', 16)->default('')->nullable()->change();
            $table->string('ig_username', 32)->default('')->nullable()->change();
        });
    }
}
