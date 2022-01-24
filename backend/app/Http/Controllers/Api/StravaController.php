<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use CodeToad\Strava\StravaFacade;
use Illuminate\Http\Request;
use App\Models\StravaUser;
use Illuminate\Support\Facades\DB;

class StravaController extends Controller
{
    public function __construct(StravaUser $stravaUser, Ride $ride)
    {
        $this->stravaUser = $stravaUser;
        $this->ride = $ride;
    }

    /**
     * @param  int         $page   ページ
     * @return object json $result 取得したルート
     *
     * STRAVAで作成したルートを取得
     */
    public function getUserRoute(int $page)
    {
        if($page < 1){
            abort(404);
        }

        $stravaUser = $this->stravaUser->getStravaUser();

        if(!$stravaUser){
            return response()->json(['result' => null]);
        }

        $results = StravaFacade::athleteRoutes($stravaUser->access_token, $stravaUser->strava_id, $page, $perPage=15);

        // APIから取得したデータをride_routeの形式に合わせて加工
        $ride_routes = [];         // 取得したルート
        $strava_route_id_arr = []; // ユーザの保存チェックに使用

        foreach($results as $i => $result){
            $ride_routes[$i]['data'] = [
                // DBに保存するデータとは異なる（保存時はstrava_route_idを送信）
                'strava_route_id'  => $result->id,
                'uuid'             => null,
                'user_uuid'        => $stravaUser->user_uuid,
                'name'             => $result->name,
                'elevation'        => floor_plus($result->elevation_gain),
                'distance'         => floor_plus($result->distance/1000),
                'lap_status'       => false,                          // ([スタート地点の座標] === [ゴール地点の座標]) 現在はセグメントでしか座標が取得できない
                'comment'          => $result->description,
                'publish_status'   => (int) $result->private * 2,     // private => false なら 0 / private => true なら 2
                'map_img_uri'      => $result->map_urls->retina_url,
                'summary_polyline' => $result->map->summary_polyline,
            ];

            $strava_route_id_arr[$i] = $result->id;
        }

        $saved_ride_routes_list = DB::table('saved_ride_routes')
            ->join('ride_routes', 'route_uuid', 'ride_routes.uuid')
            ->where('saved_ride_routes.user_uuid', $stravaUser->user_uuid)
            ->whereIn('strava_route_id', $strava_route_id_arr)
            ->get('strava_route_id');

        $ride_routes = $this->stravaUser->addIsRegisteredToRide_routes($ride_routes, $saved_ride_routes_list);
        return response()->json($ride_routes);
    }

    /**
     * Stravaのパワーデータを取得
     *
     * @var bool $byPower
     * @var float $userStrength
     *
     * @return object $data
     */
    public function getUserStrength()
    {
        $stravaUser = $this->stravaUser->getStravaUser();

        if($stravaUser->strava_id ?? false){
            $athlete = StravaFacade::athlete($stravaUser->access_token); // Stravaから連携したアスリートのデータを取得
            $userStrength = $this->stravaUser->getPowerWeightRatio($athlete); //ユーザのPWRを取得
        }else{
            $userStrength = 0.0;
        }

        if($userStrength){
            $byPower = true;
        }else{
            $byPower = false;
        }

        $data = [
            'strength' => $userStrength,
            'byPower'  => $byPower
        ];

        return response()->json($data);
    }
}

