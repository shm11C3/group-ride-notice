<?php

namespace App\Http\Controllers\Api\Ride;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Requests\CreateRideRequest;

class RideController extends Controller
{
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
            'comment' => $request['comment'],
            'publish_status' => $request['publish_status'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $data = ['status' => true];

        return response()->json($data);
    }
}
