<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequestStatusToAuthRequestIpAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auth_request_ip_address', function (Blueprint $table) {
            $table->boolean('request_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auth_request_ip_address', function (Blueprint $table) {
            $table->boolean('request_status');
        });
    }
}
