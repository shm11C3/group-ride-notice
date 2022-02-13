<?php

namespace App\Http\Controllers\Api\Ride;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Requests\CreateRideRouteRequest;
use App\Http\Requests\RegisterRideRouteRequest;
use App\Models\Ride;
use App\Models\RideRoute;

class RideRouteController extends Controller
{
    public function __construct(Ride $ride, RideRoute $rideRoute)
    {
        $this->ride = $ride;
        $this->rideRoute = $rideRoute;
    }

    /**
     * ライドルートを作成
     *
     * @param App\Http\Requests\CreateRideRouteRequest
     * @return bool
     */
    public function createRideRoute(CreateRideRouteRequest $request)
    {
        if (!is_numeric($request['strava_route_id'])) {
            return response()->json(['status' => false]);
        }

        if($request['map_img_uri']){
            // 正しいURLかチェック
            if(array_search(get_nonStrict_domain_by_hostname(parse_url($request['map_img_uri'], PHP_URL_HOST)), $this->rideRoute->allowHostList) === false){
                return response()->json([
                    'status' => false,
                    'uuid' => NULL,
                ]);
            }
        }

        $user_uuid = Auth::user()->uuid;

        // STRAVAのroute_idと一致するルートがすでに作成されている場合
        if($request['strava_route_id']){
            $exist_ride_route_uuid = DB::table('ride_routes')->where('strava_route_id', $request['strava_route_id'])->get('uuid');
            $exist_ride_route_uuid = $exist_ride_route_uuid[0]->uuid ?? false;

            if($exist_ride_route_uuid){
                $this->saveRideRoute($user_uuid, $exist_ride_route_uuid, true);
                $data = [
                    'status' => true,
                    'uuid' => (string)$exist_ride_route_uuid,
                ];

                return response()->json($data);
            }
        }

        $ride_route_uuid = Str::uuid();

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
                'publish_status' => $request['publish_status'],
                'map_img_uri' => $request['map_img_uri'],
                'strava_route_id' => (int)$request['strava_route_id'],
            ]);

            if($request['save_status']){

                //保存するを選択した場合
                $this->saveRideRoute($user_uuid, $ride_route_uuid, true);
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
     * ライドルートの登録
     *
     * @param App\Http\Requests\RegisterRideRouteRequest
     * @return response
     */
    public function registerRideRoute(RegisterRideRouteRequest $request)
    {
        $register = $this->saveRideRoute(Auth::user()->uuid, $request['ride_route_uuid'], false);

        $result = ['status' => $register];

        return response()->json($result);
    }

    /**
     * ライドルート登録テーブルに挿入
     *
     * @param string $user_uuid
     * @param string $ride_route_uuid
     * @param bool $notExist
     *
     * @return bool
     */
    public function saveRideRoute(string $user_uuid, string $ride_route_uuid, bool $notExist)
    {
        // rideRouteIsSaved() はクエリ発行するので必ず後へ
        if($notExist || !$this->ride->rideRouteIsSaved($user_uuid, $ride_route_uuid)){
            // 登録
            DB::table('saved_ride_routes')
                ->insert([
                    'route_uuid' => $ride_route_uuid,
                    'user_uuid' => $user_uuid,
                    'route_category_id' => 0,
            ]);

            return true;

        }else{
            // 解除
            DB::table('saved_ride_routes')
                ->where('user_uuid', $user_uuid)
                ->where('route_uuid', $ride_route_uuid)
                ->delete();

            return false;
        }
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
            'strava_route_id',
            'name',
            'elevation',
            'distance',
            'lap_status',
            'comment',
            'publish_status',
            'ride_routes.map_img_uri',
        ]);

        $data = ['data'=>$dbData, 'key'=>'saved_ride_routes'];

        return response()->json($data);
    }

    /**
     *  すべてのルートを取得
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

        $nextPage = $ride_routes->nextPageUrl();

        if(!isset($ride_routes[0])){
            // クエリ結果が存在しない場合
            return response()->json();
        }

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
            'ride_routes' => $result,
            'next_page_url' => $nextPage
        ];

        return response()->json($data);
    }
}
