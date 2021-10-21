<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Ride;

class DatabaseSeeder extends Seeder
{
    public $user_uuid = '241cec9d-7d70-64e2-827e-379efdff0a66';

    public $ride_uuid = 'c29754dc-46df-2630-2307-786c062d4b8b';

    public $meeting_place_uuid = '5e1d62fa-8abc-7250-1255-f342b5b87b0f';

    public $ride_route_uuid = '7ddc539b-7dca-3284-1b2b-513406f47115';

    public $ride_participant_uuid = 'c36727e4-bb22-0724-a847-b11dac7f0e19';
 

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {   
        $this->call([
            AdminTablesSeeder::class,
        ]);

        /**
         * テスト用テンプレートユーザーを作成
         */
        User::create([
            'uuid' => $this->user_uuid,
            'name' => 'テストユーザー',
            'email' => 'aaa@aaa.com',
            'password' => Hash::make('password'),
            'prefecture_code' => 14,

        ]);

        UserProfile::create([
            'user_uuid' => $this->user_uuid,
            'user_profile_img_path' => '',
            'user_intro' => 'Hello',
            'user_url' => 'https://bipokele.com',
            'fb_username' => 'facebook',
            'tw_username' => 'twitter',
            'ig_username' => 'instagram'
        ]);

        DB::table('meeting_places')->insert([
            'uuid' => $this->meeting_place_uuid,
            'user_uuid' => $this->user_uuid,
            'name' => 'test_place',
            'prefecture_code' => 14,
            'address' => '神奈川県横浜市中区日本大通１',
            'publish_status' => 0,
        ]);

        DB::table('ride_routes')->insert([
            'uuid' => $this->ride_route_uuid,
            'user_uuid' => $this->user_uuid,
            'name' => 'test_route',
            'elevation' => 400,
            'distance' => 23,
            'lap_status' => true,
            'comment' => 'あいうえお',
            'publish_status' => 0
        ]);

        Ride::create([
            'uuid' => $this->ride_uuid,
            'host_user_uuid' => $this->user_uuid,
            'meeting_places_uuid' => $this->meeting_place_uuid,
            'ride_routes_uuid' => $this->ride_route_uuid,
            'name' => 'test_ride',
            'time_appoint' => now()->addMonth(1),
            'intensity' => 5,
            'num_of_laps' => 5,
            'comment' => 'abcdefg',
            'publish_status' => 0
        ]);

        DB::table('ride_participants')->insert([
            'uuid' => $this->ride_participant_uuid,
            'user_uuid' => $this->user_uuid,
            'ride_uuid' => $this->ride_uuid,
            'comment' => 'ホストユーザー'
        ]);



        //User::factory(10)->create();
    }
}
