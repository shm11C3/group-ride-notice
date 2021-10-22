<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Ride;

class ConstantDatabaseValue extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seeder = new DatabaseSeeder;

        /**
         * テスト用テンプレートユーザーを作成
         */
        User::create([
            'uuid' => $seeder->user_uuid,
            'name' => 'テストユーザー',
            'email' => 'aaa@aaa.com',
            'password' => Hash::make('password'),
            'prefecture_code' => 14,

        ]);

        UserProfile::create([
            'user_uuid' => $seeder->user_uuid,
            'user_profile_img_path' => '',
            'user_intro' => 'Hello',
            'user_url' => 'https://bipokele.com',
            'fb_username' => 'facebook',
            'tw_username' => 'twitter',
            'ig_username' => 'instagram'
        ]);

        DB::table('meeting_places')->insert([
            'uuid' => $seeder->meeting_place_uuid,
            'user_uuid' => $seeder->user_uuid,
            'name' => 'test_place',
            'prefecture_code' => 14,
            'address' => '神奈川県横浜市中区日本大通１',
            'publish_status' => 0,
        ]);

        DB::table('ride_routes')->insert([
            'uuid' => $seeder->ride_route_uuid,
            'user_uuid' => $seeder->user_uuid,
            'name' => 'test_route',
            'elevation' => 400,
            'distance' => 23,
            'lap_status' => true,
            'comment' => 'あいうえお',
            'publish_status' => 0
        ]);

        Ride::create([
            'uuid' => $seeder->ride_uuid,
            'host_user_uuid' => $seeder->user_uuid,
            'meeting_places_uuid' => $seeder->meeting_place_uuid,
            'ride_routes_uuid' => $seeder->ride_route_uuid,
            'name' => 'test_ride',
            'time_appoint' => now()->addMonth(1),
            'intensity' => 5,
            'num_of_laps' => 5,
            'comment' => 'abcdefg',
            'publish_status' => 0
        ]);

        DB::table('ride_participants')->insert([
            'uuid' => $seeder->ride_participant_uuid,
            'user_uuid' => $seeder->user_uuid,
            'ride_uuid' => $seeder->ride_uuid,
            'comment' => 'ホストユーザー'
        ]);
    }
}
