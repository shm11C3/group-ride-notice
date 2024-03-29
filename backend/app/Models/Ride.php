<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Ride extends Model
{
    use HasFactory;

    protected $appends = [
        'rideParticipant_count',
        'rideParticipant_user',
        'hadByLoginUser',
    ];

    public $paginate = [1, 30];

    public function rideParticipants()
    {
      return $this->hasMany(RideParticipant::class, 'ride_uuid', 'uuid');
    }

    /**
     * ridesと紐付いたrideParticipantの総数を取得
     *
     * @return int
     */
    public function getRideParticipantCountAttribute()
    {
        return $this->rideParticipants->count();
    }

    /**
     * ログインユーザーがride_participantsに存在するかチェック
     *
     * @param void
     * @return bool
     */
    public function getRideParticipantUserAttribute()
    {
        return $this->rideParticipants->contains(function ($user) {
            $auth = Auth::user()->uuid ?? 0;
            return $user->user_uuid === $auth;
        });
    }

    /**
     * ride_routes_uuidとuser_uuidが一致した場合trueを返す
     *
     * @param void
     * @return bool
     */
    public function getHadByLoginUserAttribute()
    {
        $auth = Auth::user()->uuid ?? 0;
        return $this->host_user_uuid === $auth;
    }

    /**
     * $user_idとログインユーザの一致チェック
     *
     * @param string $user_id
     * @return bool
     */
    public function isAuthUser($user_id)
    {
        $loginUser_id = Auth::user()->uuid ?? '';

        if($user_id === $loginUser_id) {
            return true;
        }
        return false;
    }

    /**
     * 都道府県コードからSQLの演算子を返す
     *
     * @param int $code
     * @return string
     */
    public function getOperatorByPrefectureCode($code)
    {
        if($code<1 || $code>47){
            return '!=';
        }
        return '=';
    }

    /**
     * 引数からsqlに用いる時間の最小値と最大値の配列を返す
     *
     * @param int $time_appoint
     * @return array $result
     */
    public function createTimeSql($time_appoint)
    {
        $now = date('Y-m-d H:i:s', strtotime('now'));

        //時間の最小値
        $requestArr_min = [
            $now,                                             //すべて
            $now,                                             //今日中
            date('Y-m-d', strtotime('now 1day')).' 00:00:00', //明日中
            date('Y-m-d', strtotime('now 2day')).' 00:00:00', //1週間以内
            date('Y-m-d', strtotime('now 8day')).' 00:00:00', //1ヶ月以内
            date('2021-06-26 19:30:00')                       //過去全て
        ];

        //時間の最小値
        $requestArr_max = [
            date('Y-m-d', strtotime('now 2year')).' 23:59:59',  //すべて
            date('Y-m-d', strtotime('now')).' 23:59:59',        //今日中
            date('Y-m-d', strtotime('now 1day')).' 23:59:59',   //明日中
            date('Y-m-d', strtotime('now 1week')).' 23:59:59',  //1週間以内
            date('Y-m', strtotime('now 1month')).'-01 23:59:59', //1ヶ月以内
            $now
        ];


        if($time_appoint >= count($requestArr_min)){
            //存在しないキーの場合0を代入
            $time_appoint = 0;
        }


        $result = [
            $requestArr_min[$time_appoint],
            $requestArr_max[$time_appoint]
        ];

        return $result;
    }

    /**
     * 引数からsqlに用いる強度の最小値と最大値の配列を返す
     *
     * @param int $intstRange
     * @return array $result
     */
    public function getIntstByRange($intstRange)
    {
        $intst_min = [
            0, 0, 2, 4, 7
        ];

        $intst_max = [
            10, 1, 3, 6, 10,
        ];


        if($intstRange >= count($intst_min)){
            //存在しないキーの場合0を代入
            $intstRange = 0;
        }


        $result = [
            $intst_min[$intstRange],
            $intst_max[$intstRange]
        ];

        return $result;
    }

    /**
     * 保存されている集合場所を配列で返す
     *
     * @param object $meeting_places_dbData
     * @param string $user_uuid
     *
     * @return array $registeredMeetingPlaces_arr
     */
    public function isRegisteredMeetingPlace(object $meeting_places_dbData, string $user_uuid)
    {
        $sql = DB::table('saved_meeting_places');

        foreach($meeting_places_dbData as $meeting_place_dbData){
            $sql->orWhere('meeting_place_uuid', $meeting_place_dbData->uuid)->where('user_uuid', $user_uuid);
        }

        $registeredMeetingPlaces = $sql->orderBy('id' ,'desc')
        ->get('meeting_place_uuid');

        $registeredMeetingPlaces_arr = $this->registeredMeetingPlacesToArray($registeredMeetingPlaces);

        return $registeredMeetingPlaces_arr;
    }

    /**
     * registeredMeetingPlacesを配列に変換
     *
     * @param $requests
     * @return $results
     */
    public function registeredMeetingPlacesToArray(object $registeredMeetingPlaces)
    {
        $requests = $registeredMeetingPlaces;
        $results = [];

        foreach($requests as $i => $request){
            $results[$i] = $request->meeting_place_uuid;
        }

        return $results;
    }

    /**
     * 集合場所が保存されているか確認
     *
     * @param string $user_uuid
     * @param string $meeting_place_uuid
     *
     * @return bool
     */
    public function meetingPlaceIsSaved(string $user_uuid, string $meeting_place_uuid)
    {
        $isExist = DB::table('saved_meeting_places')
            ->where('user_uuid', $user_uuid)
            ->where('meeting_place_uuid', $meeting_place_uuid)
            ->exists();

        if($isExist){
            return true;
        }
        return false;
    }

    /**
     * 保存されているルートを配列で返す
     *
     * @param object $ride_routes
     * @param string $user_uuid
     *
     * @return array $registeredRideRoute_arr
     */
    public function isRegisteredRideRoute(object $ride_routes, string $user_uuid)
    {
        $sql = DB::table('saved_ride_routes');

        foreach($ride_routes as $ride_route){
            $sql->orWhere('route_uuid', $ride_route->uuid)->where('user_uuid', $user_uuid);
        }

        $registeredRide_routes = $sql->orderBy('id' ,'desc')
        ->get('route_uuid');

        $ride_routes_arr = $this->registeredRideRoutesToArray($registeredRide_routes);

        return $ride_routes_arr;

    }

    /**
     * ride_routesを配列に変換
     *
     * @param $requests
     * @return $results
     */
    public function registeredRideRoutesToArray(object $registeredRideRoutes)
    {
        $requests = $registeredRideRoutes;
        $results = [];

        foreach($requests as $i => $request){
            $results[$i] = $request->route_uuid;
        }

        return $results;
    }

    /**
     * ライドルートが保存されているか確認
     *
     * @param string $user_uuid
     * @param string $ride_route_uuid
     *
     * @return bool
     */
    public function rideRouteIsSaved(string $user_uuid, string $ride_route_uuid)
    {
        $isExist = DB::table('saved_ride_routes')
            ->where('user_uuid', $user_uuid)
            ->where('route_uuid', $ride_route_uuid)
            ->exists();


        if($isExist){
            return true;
        }
        return false;
    }
}
