<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('user_intro', 128)->default('')->change();
            $table->string('user_url', 128)->default('')->change();
            $table->string('fb_username', 64)->default('');
            $table->string('tw_username', 16)->default('');
            $table->string('ig_username', 32)->default('');
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
            $table->string('user_intro', 128)->default(false)->change();
            $table->string('user_url', 128)->default(false)->change();
            $table->string('fb_username', 64)->default('');
            $table->string('tw_username', 16)->default('');
            $table->string('ig_username', 32)->default('');
        });
    }
}
