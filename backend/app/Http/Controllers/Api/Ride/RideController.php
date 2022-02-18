<?php

namespace App\Http\Controllers\Api\Ride;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Requests\CreateRideRequest;
use App\Http\Requests\UpdateRideRequest;
use App\Http\Requests\UpdatePublishStatusRequest;
use App\Models\Follow;
use App\Models\Ride;
use App\Models\User;

class RideController extends Controller
{
    public function __construct(Ride $ride, Follow $follow, User $user)
    {
        $this->ride = $ride;
        $this->follow = $follow;
        $this->user = $user;
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
        $pt_uuid = Str::uuid();
        $user_uuid = Auth::user()->uuid;

        DB::beginTransaction();
        try{

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

            DB::table('ride_participants')
                ->insert([
                    'uuid' => $pt_uuid,
                    'user_uuid' => $user_uuid,
                    'ride_uuid' => $ride_uuid,
                    'comment' => 'ホストユーザー'
                ]);

            DB::commit();
        }catch(\Throwable $e){
            DB::rollback();
            abort(500);
        }

        $data = ['status' => true, 'ride_uuid' => $ride_uuid];

        return response()->json($data);
    }

    /**
     * ライドをアップデート
     *
     * @param App\Http\Requests\UpdateRideRequest
     * @return response
     */
    public function updateRide(UpdateRideRequest $request)
    {
        $user_uuid = Auth::user()->uuid;

        DB::table('rides')
            ->where('uuid', $request['uuid'])
            ->where('host_user_uuid', $user_uuid)
            ->update([
                'ride_routes_uuid' => $request['ride_routes_uuid'],
                'name' => $request['name'],
                'intensity' => $request['intensity'],
                'num_of_laps' => $request['num_of_laps'],
                'comment' => $request['comment'],
                'updated_at' => now(),
            ]);

        $data = ['status' => true];

        return response()->json($data);
    }

    /**
     * ライドの公開ステータスを更新
     *
     * @param App\Http\Requests\UpdatePublishStatusRequest;
     * @return response
     */
    public function updatePublishStatus(UpdatePublishStatusRequest $request)
    {
        $user_uuid = Auth::user()->uuid;

        DB::table('rides')
            ->where('uuid', $request['uuid'])
            ->where('host_user_uuid', $user_uuid)
            ->update([
                'publish_status' => $request['publish_status']
            ]);

        $data = ['status' => true];

        return response()->json($data);
    }

    /**
     * ライドを取得
     *
     * $filterFollowが真の場合はフォロー済みのライドのみを取得
     *
     * @param int $time_appoint
     * @param int $prefecture_code
     * @param int $intensityRange
     * @param int $filterFollow
     *
     * @return object $rides
     */
    public function getRides(int $time_appoint, int $prefecture_code, int $intensityRange, bool $filterFollow)
    {
        $user_uuid = Auth::user()->uuid ?? 0;

        $time = $this->ride->createTimeSql($time_appoint);
        $operator = $this->ride->getOperatorByPrefectureCode($prefecture_code);
        $intensity = $this->ride->getIntstByRange($intensityRange);

        $query_rides = Ride::with('rideParticipants');

        $query_public_rides = $query_rides->where('rides.publish_status', 0) // 公開設定
            ->where('time_appoint', '>', $time[0])
            ->where('time_appoint', '<', $time[1])
            ->where('meeting_places.prefecture_code', $operator, $prefecture_code)
            ->where('intensity', '>=', $intensity[0])
            ->where('intensity', '<=', $intensity[1]);

        if($user_uuid){
            // ログイン済みの場合
            $followers_arr = $this->follow->followers_to_arr($this->follow->getFollowersBy_user_uuid($user_uuid));

            if($filterFollow){
                // フォロー内から取得
                $follows = $this->follow->getFollowsBy_user_uuid($user_uuid);
                if(!isset($follows[0])){
                    // 存在しない場合は空値を戻す
                    $data = [
                        'rides' => [],
                        'user_uuid' => $user_uuid
                    ];
                    return response()->json($data);
                }

                $query_rides->whereIn('host_user_uuid', $this->follow->follows_to_arr($follows))
                    ->where('rides.publish_status', 0)
                    ->where('time_appoint', '>', $time[0])
                    ->where('time_appoint', '<', $time[1])
                    ->where('meeting_places.prefecture_code', $operator, $prefecture_code)
                    ->where('intensity', '>=', $intensity[0])
                    ->where('intensity', '<=', $intensity[1])

                    ->orWhereIn('host_user_uuid', $followers_arr)
                    ->whereIn('host_user_uuid', $this->follow->follows_to_arr($follows))
                    ->where('rides.publish_status', 1)
                    ->where('time_appoint', '>', $time[0])
                    ->where('time_appoint', '<', $time[1])
                    ->where('meeting_places.prefecture_code', $operator, $prefecture_code)
                    ->where('intensity', '>=', $intensity[0])
                    ->where('intensity', '<=', $intensity[1]);

            }else{
                // ログイン済み かつ フォロー内外から取得時

                $query_rides = $query_public_rides->orWhere('host_user_uuid', $user_uuid) // 自分の投稿したライド (すべての公開設定)
                    ->where('time_appoint', '>', $time[0])
                    ->where('time_appoint', '<', $time[1])
                    ->where('meeting_places.prefecture_code', $operator, $prefecture_code)
                    ->where('intensity', '>=', $intensity[0])
                    ->where('intensity', '<=', $intensity[1])

                    ->orWhereIn('host_user_uuid', $followers_arr) // フォロワーの投稿したライド (限定公開)
                    ->where('rides.publish_status', 1)
                    ->where('time_appoint', '>', $time[0])
                    ->where('time_appoint', '<', $time[1])
                    ->where('meeting_places.prefecture_code', $operator, $prefecture_code)
                    ->where('intensity', '>=', $intensity[0])
                    ->where('intensity', '<=', $intensity[1]);
            }
        }else{
            // 非ログイン時
            $query_rides = $query_public_rides;
        }

        $rides = $query_rides->join('meeting_places', 'meeting_places.uuid', 'meeting_places_uuid')
        ->join('ride_routes', 'ride_routes.uuid', 'ride_routes_uuid')
        ->join('users', 'host_user_uuid', 'users.uuid')
        ->join('user_profiles', 'user_profiles.user_uuid', 'users.uuid')
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
            'users.name as user_name',
            'user_profile_img_path',
            'ride_routes.map_img_uri',
        ])
        ->simplePaginate(100);

        $data = [
            'rides' => $rides,
            'user_uuid' => $user_uuid,
        ];

        return response()->json($data);
    }

    /**
     * ユーザー関連ライドをすべて取得
     *
     * @param void
     * @return object $rides
     */
    public function getRidesRelatedToAuthorizedUser(int $option)
    {
        $user_uuid = Auth::user()->uuid;
        $paginate = $this->ride->paginate[$option];

        $rides = DB::table('ride_participants')
        ->where('time_appoint', '>', now())
        ->where('ride_participants.user_uuid', $user_uuid)
        ->join('rides', 'rides.uuid', 'ride_uuid')
        ->join('meeting_places', 'meeting_places.uuid', 'meeting_places_uuid')
        ->join('ride_routes', 'ride_routes.uuid', 'ride_routes_uuid')
        ->orderBy('time_appoint' ,'asc')
        ->select([
            'rides.uuid',
            'host_user_uuid',
            'rides.name as ride_name',
            'time_appoint',
            'intensity',
            'num_of_laps',
            'rides.publish_status',
            'meeting_places.name as mp_name',
            'prefecture_code',
            'ride_routes.name as rr_name',
            'elevation',
            'distance',
            'ride_routes.map_img_uri',
        ])
        ->simplePaginate($paginate);

        $data = [
            'rides' => $rides,
            'user_uuid' => $user_uuid
        ];

        return response()->json($data);
    }

    /**
     * rides.uuidからライドを取得
     *
     * @param string uuid
     * @return response
     */
    public function getRideBy_rides_uuid(string $uuid)
    {
        $auth_uuid = Auth::user()->uuid ?? 0;

        $ride = Ride::with('rideParticipants.user.userProfile')
        ->where('rides.uuid', $uuid)
        ->where('rides.publish_status', '<', 2)
        ->orWhere('host_user_uuid', $auth_uuid)
        ->where('rides.uuid', $uuid)
        ->join('meeting_places', 'meeting_places.uuid', 'meeting_places_uuid')
        ->join('ride_routes', 'ride_routes.uuid', 'ride_routes_uuid')
        ->get([
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
            'ride_routes.map_img_uri',
        ]);

        foreach($ride[0]->rideParticipants as $rideParticipant){
            if(!$rideParticipant->user->userProfile->user_profile_img_path){
                $rideParticipant->user->userProfile->user_profile_img_path = asset($this->user->userDefaultImgPath[75]);
            }
        }

        return response()->json($ride);
    }

    /**
     * rides.uuidと一致かつログインユーザのライドを取得
     *
     * @param string uuid
     * @return response
     */
    public function getAuthorizedRideBy_rides_uuid(string $uuid)
    {
        $user_uuid = Auth::user()->uuid;

        $ride = Ride::with('rideParticipants.user')
        ->where('rides.uuid', $uuid)
        ->where('host_user_uuid', $user_uuid)
        ->join('meeting_places', 'meeting_places.uuid', 'meeting_places_uuid')
        ->join('ride_routes', 'ride_routes.uuid', 'ride_routes_uuid')
        ->join('users', 'host_user_uuid', 'users.uuid')
        ->get([
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
            'ride_routes.map_img_uri',
        ]);

        return response()->json($ride);
    }

    /**
     * ユーザーが作成したライドを取得
     *
     * @param string $user_uuid
     * @return Response
     */
    public function getUserRides(string $user_uuid)
    {
        $query_rides = Ride::with('rideParticipants');

        $auth_uuid = Auth::user()->uuid ?? 0;
        if(!$auth_uuid){
            $query_rides->where('host_user_uuid', $user_uuid)->where('rides.publish_status', 0);

        }elseif($user_uuid === $auth_uuid){
            $query_rides->where('host_user_uuid', $user_uuid);

        }else{
            // ログイン時・他のユーザ
            $isFollower = array_search($user_uuid, $this->follow->followers_to_arr($this->follow->getFollowersBy_user_uuid($auth_uuid)));

            if($isFollower !== false){
                $query_rides->where('host_user_uuid', $user_uuid)->where('rides.publish_status', 0)
                    ->orWhere('host_user_uuid', $user_uuid)->where('rides.publish_status', 1);
            }else{
                $query_rides->where('host_user_uuid', $user_uuid)->where('rides.publish_status', 0);
            }
        }

        $rides = $query_rides->join('meeting_places', 'meeting_places.uuid', 'meeting_places_uuid')
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
                'users.name as user_name',
                'ride_routes.map_img_uri',
            ])
            ->simplePaginate(100);

        return response()->json($rides);
    }
}
