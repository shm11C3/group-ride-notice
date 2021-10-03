<?php

namespace App\Http\Controllers\Api\Ride;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Requests\CreateRideRequest;
use App\Http\Requests\ParticipationRequest;
use App\Models\User;
use App\Models\Ride;
use Dotenv\Parser\Value;

class RideController extends Controller
{
    public function __construct(Ride $ride)
    {
        $this->ride = $ride;
    }

    /**
     * ライドを作成
     * 
     * @param App\Http\Requests\CreateRideRequest
     * @return bool
     */
    public function createRide(CreateRideRequest $request)
    {
        $ride_uuid = Str::uuid();
        $user_uuid = Auth::user()->uuid;

        DB::table('rides')
        ->insert([
            'uuid' => $ride_uuid,
            'host_user_uuid' => $user_uuid,
            'meeting_places_uuid' => $request['meeting_places_uuid'],
            'ride_routes_uuid' => $request['ride_routes_uuid'],
            'name' => $request['name'],
            'time_appoint' => $request['time_appoint'],
            'intensity' => $request['intensity'],
            'num_of_laps' => $request['num_of_laps'],
            'comment' => $request['comment'],
            'publish_status' => $request['publish_status'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $data = ['status' => true];

        return response()->json($data);
    }

    /**
     * ライドを取得
     * 
     * @param void
     * @return object $rides
     */
    public function getRides($time_appoint, $prefecture_code, $intensityRange)
    {        
        $user = Auth::user();
        $user_uuid = $user->uuid ?? 0;


        $time = $this->ride->createTimeSql($time_appoint);
        $operator = $this->ride->getOpeByCode($prefecture_code);
        $intensity = $this->ride->getIntstByRange($intensityRange);


        $rides = DB::table('rides')

            //rides.publish_status = 0
                ->where('rides.publish_status', 0)
                ->where('time_appoint', '>', $time[0])
                ->where('time_appoint', '<', $time[1])
                ->where('meeting_places.prefecture_code', $operator, $prefecture_code)
                ->where('intensity', '>=', $intensity[0])
                ->where('intensity', '<=', $intensity[1])
            //host_user_uuid = $user_uuid
                ->orWhere('host_user_uuid', $user_uuid)
                ->where('time_appoint', '>', $time[0])
                ->where('time_appoint', '<', $time[1])
                ->where('meeting_places.prefecture_code', $operator, $prefecture_code)
                ->where('intensity', '>=', $intensity[0])
                ->where('intensity', '<=', $intensity[1])

            ->join('meeting_places', 'meeting_places.uuid', 'meeting_places_uuid')
            ->join('ride_routes', 'ride_routes.uuid', 'ride_routes_uuid')
            ->join('users', 'host_user_uuid', 'users.uuid')
            ->orderBy('rides.created_at' ,'desc')
            ->select([
                'rides.uuid',
                'host_user_uuid',
                'meeting_places_uuid',
                'ride_routes_uuid',
                'rides.name as ride_name',
                'time_appoint',
                'intensity',
                'num_of_laps',
                'rides.comment as ride_comment',
                'rides.publish_status',
                'rides.created_at',
                'rides.updated_at',
                'meeting_places.name as mp_name',
                'meeting_places.prefecture_code',
                'address',
                'ride_routes.name as rr_name',
                'elevation',
                'distance',
                'ride_routes.comment as rr_comment',
                'users.name as user_name'
            ])
            ->simplePaginate(30);

        return response()->json($rides);
    }

    /**
     * 自分で作成したライドをすべて取得
     * 
     * @param void
     * @return object $rides
     */
    public function getRegisteredRides()
    {
        $user = Auth::user();

        $rides = DB::table('rides')
        ->where('host_user_uuid', $user->uuid)
        ->join('meeting_places', 'meeting_places.uuid', 'meeting_places_uuid')
        ->join('ride_routes', 'ride_routes.uuid', 'ride_routes_uuid')
        ->orderBy('rides.created_at' ,'desc')
        ->select('*')
        ->simplePaginate(30);

        return response()->json($rides);
    }

    
    /**
     * ライドの参加登録
     * 
     * @param App\Http\Requests\ParticipationRequest;
     * @return response
     */
    public function participationRegister(ParticipationRequest $request)
    {
        $user = Auth::user();
        $uuid = Str::uuid();

        DB::table('ride_participants')
            ->insert([
                'uuid' => $uuid,
                'user_uuid' => $user->uuid,
                'ride_uuid' => $request['ride_uuid'],
                'comment' => $request['comment']
            ]);

        return response()->json(['status' => true, 'uuid' => $uuid]);
    }
}
