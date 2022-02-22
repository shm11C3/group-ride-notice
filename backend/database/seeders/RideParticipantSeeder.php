<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RideParticipantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rides = DB::table('rides')->get(['host_user_uuid', 'uuid']);
        $ride_participants = DB::table('ride_participants')->join('rides', 'ride_participants.ride_uuid', 'rides.uuid')->get(['rides.uuid', 'rides.host_user_uuid', 'ride_participants.user_uuid']);

        $participant_list = [];
        foreach($ride_participants as $i => $ride_participant){
            if($ride_participant->host_user_uuid === $ride_participant->user_uuid){
                $participant_list[$i] = $ride_participant->uuid;
            }
        }

        foreach($rides as $i => $ride){
            $result = false;
            foreach($participant_list as $participant){
                if($participant === $ride->uuid){
                    $result = true;
                    echo('exist ');
                }
            }
            if(!$result){
                $uuid = Str::uuid();

                DB::table('ride_participants')->insert([
                    'user_uuid' => $ride->host_user_uuid,
                    'ride_uuid' => $ride->uuid,
                    'comment' => 'テスト参加',
                    'uuid' => $uuid
                ]);

                echo($i.' ');
            }
        }
    }
}
