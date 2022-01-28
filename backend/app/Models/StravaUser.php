<?php

namespace App\Models;

use Carbon\Carbon;
use CodeToad\Strava\StravaFacade;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StravaUser extends Model
{
    use HasFactory;

    /**
     * PWRを算出
     * データがない場合は値を返さない
     *
     * @param object $athlete
     *
     * @return float
     * @return void
     */
    public function getPowerWeightRatio(object $athlete)
    {
        if(property_exists($athlete, 'ftp') && property_exists($athlete, 'weight')){
            // weightの単位はkgだが今後のAPI仕様次第でヤードポンド法にも対応させる必要あり
            return floor_plus($athlete->ftp / $athlete->weight);
        }
    }

    /**
     * strava_idから連携しているユーザidを返す
     *
     * @param  int $strava_id stravaアカウントのid
     * @return int $user_id   bipokeleアカウントのid
     */
    public function getUserIdByStravaId(int $strava_id)
    {
        $user_id = DB::table('strava_users')
            ->join('users', 'users.uuid', 'user_uuid')
            ->where('strava_id', $strava_id)
            ->get('users.id');

        return $user_id[0]->id ?? null;
    }

    /**
     * アクセストークンを取得
     *
     * @var object $refresh 更新後のトークン
     *
     * @param void
     * @return object $stravaUser ユーザデータとトークン
     */
    public function getStravaUser()
    {
        $stravaUser = Auth::user()->stravaUser;

        // アクセストークンの更新が必要ない場合はそのまま値を帰す
        if(!$stravaUser || strtotime(Carbon::now()) < $stravaUser->expires_at){
            return $stravaUser;
        }

        $refresh = StravaFacade::refreshToken($stravaUser->refresh_token); // トークンの更新

        DB::table('strava_users')
            ->where('user_uuid', $stravaUser->user_uuid)
            ->update([
                'access_token' => $refresh->access_token,
                'refresh_token' => $refresh->refresh_token,
                'expires_at' => $refresh->expires_at,
            ]);

        $stravaUser->access_token  = $refresh->access_token;
        $stravaUser->refresh_token = $refresh->refresh_token;
        $stravaUser->expires_at    = $refresh->expires_at;

        return $stravaUser;
    }

    /**
     * ride_routeで保存されているSTRAVAルートをarray $ride_routesに追加
     *
     * @param  array  $ride_routes
     * @param  object $saved_ride_routes_list
     * @return array  $ride_routes
     */
    public function addIsRegisteredToRide_routes(array $ride_routes, object $saved_ride_routes_list)
    {
        $saved_ride_routes_arr = []; // 保存済みルートの`strava_route_id`リスト

        foreach($saved_ride_routes_list as $i => $saved_ride_route){
            $saved_ride_routes_arr[$i] = (array)$saved_ride_route;
        }
        foreach($ride_routes as $i => $ride_route){
            $ride_routes[$i] += ['isRegistered' => in_array(['strava_route_id' => $ride_route['data']['strava_route_id']], $saved_ride_routes_arr, /*$strict=*/ true)];
        }

        return $ride_routes;
    }
}
