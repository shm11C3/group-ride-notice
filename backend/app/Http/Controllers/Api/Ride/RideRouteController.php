<?php

namespace App\Http\Controllers\Api\Ride;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Requests\CreateRideRouteRequest;
use App\Models\Ride;

class RideRouteController extends Controller
{
    public function __construct(Ride $ride)
    {
        $this->ride = $ride;
    }

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

    /**
     * 
     * @param int $lap_status
     * @return response
     */
    public function getAllRideRoutes(int $lap_status)
    {
        $user = Auth::user();
        $user_uuid = $user->uuid ?? 0;


        $ride_routes = DB::table('ride_routes')
            ->where('lap_status', $lap_status)
            ->where('publish_status', 0) // 公開されているルート
            ->orWhere('lap_status', $lap_status)
            ->where('ride_routes.user_uuid', $user_uuid) // ユーザ自身のルート
            ->orderBy('id' ,'desc')
            ->select('*')
            ->simplePaginate(60);

        // (array) 保存済みのルート
        $registeredRideRoutes = $this->ride->isRegisteredRideRoute($ride_routes, $user_uuid);

        foreach($ride_routes as $i => $ride_route) {
            $ride_route_uuid = $ride_route->uuid;

            if(array_search($ride_route_uuid, $registeredRideRoutes) !== false ) {
                $registered = true; // 登録済みの場合

            }else{
                $registered = false; // 登録済みでない場合
            }

            // 結果からオブジェクトを作成
            $result[$i] = (object) [
                'data' => $ride_route,
                'isRegistered' => $registered
            ];
        }

        $data = [
            'auth_uuid' => $user_uuid,
            'ride_routes' => $result
        ];

        return response()->json($data);

        dd($registeredRideRoutes);



    }
}
