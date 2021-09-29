<?php

namespace App\Http\Controllers\Api\Ride;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Requests\CreateRideRouteRequest;

class RideRouteController extends Controller
{
    /**
     * ライドルートを作成
     * 
     * @param App\Http\Requests\CreateRideRouteRequest
     * @return bool
     */
    public function createRideRoute(CreateRideRouteRequest $request)
    {
        $ride_route_uuid = Str::uuid();
        $user_uuid = Auth::user()->uuid;

        DB::beginTransaction();
        try{
            DB::table('ride_routes')
            ->insert([
                'uuid' => $ride_route_uuid,
                'user_uuid' => $user_uuid,
                'name' => $request['name'],
                'elevation' => $request['elevation'],
                'distance' => $request['distance'],
                'lap_status' => $request['lap_status'],
                'comment' => $request['comment'],
                'publish_status' => $request['publish_status']
            ]);

            if($request['save_status']){
        
                //保存するを選択した場合
                DB::table('saved_ride_routes')
                ->insert([
                    'route_uuid' => $ride_route_uuid,
                    'user_uuid' => $user_uuid,
                    'route_category_id' => 0,
                ]);
            }

            $data = [
                'status' => true,
                'uuid' => $ride_route_uuid,
            ];

            DB::commit();
        }catch(\Throwable $e){
            DB::rollback();
            abort(500);
        }

        return response()->json($data);
    }

    /**
     * 保存したルートを取得
     * 
     * @param void
     * @return object $data
     */
    public function getSavedRideRoutes()
    {
        $user_uuid = Auth::user()->uuid;

        $dbData = DB::table('saved_ride_routes')
        ->where('saved_ride_routes.user_uuid', $user_uuid)
        ->join('ride_routes','route_uuid','ride_routes.uuid')
        ->get([
            'ride_routes.uuid',
            'ride_routes.user_uuid',
            'name',
            'elevation',
            'distance',
            'lap_status',
            'comment',
            'publish_status',
        ]);
        
        $data = ['data'=>$dbData, 'key'=>'saved_ride_routes'];

        return response()->json($data);
    }
}
