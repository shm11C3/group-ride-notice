<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use Illuminate\Http\Request;
use App\Models\Search;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Ride;

class SearchController extends Controller
{
    public function __construct(Search $search, User $user, Follow $follow)
    {
        $this->search = $search;
        $this->user = $user;
        $this->follow = $follow;
    }

    /**
     * 一致するライドを取得
     *
     * @param array $keywords
     * @param int $per_page_rides
     *
     * @return object $rides
     */
    private function queryRides(array $keywords, int $per_page_rides)
    {
        $auth_user_uuid = Auth::user()->uuid ?? 0;

        $query_rides = Ride::with('rideParticipants.user')
            ->join('meeting_places', 'meeting_places.uuid', 'meeting_places_uuid')
            ->join('ride_routes', 'ride_routes.uuid', 'ride_routes_uuid')
            ->join('users', 'host_user_uuid', 'users.uuid');

        if($auth_user_uuid){
            $followers = $this->follow->followers_to_arr($this->follow->getFollowersBy_user_uuid($auth_user_uuid));

            foreach($keywords as $keyword){
                $query_rides
                    ->where('rides.name', 'LIKE', $keyword)->where('rides.publish_status', 0)
                    ->orWhere('rides.comment', 'LIKE', $keyword)->where('rides.publish_status', 0)
                    ->orWhere('meeting_places.name', 'LIKE', $keyword)->where('rides.publish_status', 0)
                    ->orWhere('meeting_places.address', 'LIKE', $keyword)->where('rides.publish_status', 0)
                    ->orWhere('ride_routes.name', 'LIKE', $keyword)->where('rides.publish_status', 0)
                    ->orWhere('ride_routes.comment', 'LIKE', $keyword)->where('rides.publish_status', 0)

                    ->where('rides.name', 'LIKE', $keyword)->where('rides.publish_status', 1)->whereIn('host_user_uuid', $followers)
                    ->orWhere('rides.comment', 'LIKE', $keyword)->where('rides.publish_status', 1)->whereIn('host_user_uuid', $followers)
                    ->orWhere('meeting_places.name', 'LIKE', $keyword)->where('rides.publish_status', 1)->whereIn('host_user_uuid', $followers)
                    ->orWhere('meeting_places.address', 'LIKE', $keyword)->where('rides.publish_status', 1)->whereIn('host_user_uuid', $followers)
                    ->orWhere('ride_routes.name', 'LIKE', $keyword)->where('rides.publish_status', 1)->whereIn('host_user_uuid', $followers)
                    ->orWhere('ride_routes.comment', 'LIKE', $keyword)->where('rides.publish_status', 1)->whereIn('host_user_uuid', $followers);

            }

        }else{
            foreach($keywords as $keyword){
                $query_rides
                    ->where('rides.name', 'LIKE', $keyword)->where('rides.publish_status', 0)
                    ->orWhere('rides.comment', 'LIKE', $keyword)->where('rides.publish_status', 0)
                    ->orWhere('meeting_places.name', 'LIKE', $keyword)->where('rides.publish_status', 0)
                    ->orWhere('meeting_places.address', 'LIKE', $keyword)->where('rides.publish_status', 0)
                    ->orWhere('ride_routes.name', 'LIKE', $keyword)->where('rides.publish_status', 0)
                    ->orWhere('ride_routes.comment', 'LIKE', $keyword)->where('rides.publish_status', 0);
            }
        }

        $rides = $query_rides->select([
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
        ->simplePaginate($per_page_rides);

        return $rides;
    }

    /**
     * 一致するユーザを取得
     *
     * @param array $keywords
     * @param int $per_page_users
     *
     * @return object $users
     */
    private function queryUsers(array $keywords, int $per_page_users)
    {
        $query_users = User::with('followers.user')
            ->join('user_profiles', 'user_uuid', 'users.uuid');

        foreach($keywords as $keyword){
            $query_users->where('name', 'LIKE', $keyword)
            ->orWhere('user_intro', 'LIKE', $keyword)
            ->orWhere('fb_username', 'LIKE', $keyword)
            ->orWhere('tw_username', 'LIKE', $keyword)
            ->orWhere('ig_username', 'LIKE', $keyword);
        }

        $users = $query_users->select([
            'users.uuid',
            'users.uuid as user_uuid',
            'name',
            'prefecture_code',
            'user_intro',
            'user_profile_img_path',
            'user_intro',
            'user_url',
            'fb_username',
            'tw_username',
            'ig_username',
        ])
        ->simplePaginate($per_page_users);


        if(!isset($users[0])) {
            return $users;
        }

        // userFollowedの取得
        foreach($users as $user) {
            $user->userFollowed = false;

            if(!empty($user->followers[0])) {
                // フォロワーが存在する場合確認を行い値を代入
                $user->userFollowed = $this->user->getUserFollowed($user->followers, Auth::user()->uuid);
            }
        }

        return $users;
    }

    /**
     * 検索用API
     *
     * @param string $keyword
     * @param string $option
     *
     * @return response
     */
    public function search(string $keyword, string $option, Request $request_param)
    {
        $page = (int) $request_param->page;

        // 取得するレコード数
        if($page === 1){
            $per_page_rides = 5;
        }else{
            $per_page_rides = 60;
        }

        if($page === 1){
            $per_page_users = 5;
        }else{
            $per_page_users = 60;
        }

        $keywords_arr = $this->search->generateSearchArray($keyword); //検索キーワード配列

        // $optionの値に応じてクエリメソッドを呼び出す
        if($option === 'all' || $option === 'rides'){
            $rides = $this->queryRides($keywords_arr, $per_page_rides);
        }else{
            $rides = (object) ['status' => false, 'data' => []];
        }

        if($option === 'all' || $option === 'users'){
            $users = $this->queryUsers($keywords_arr, $per_page_users);
        }else{
            $users = (object) ['status' => false, 'data' => []];
        }

        $data = [
            'rides' => $rides,
            'users' => $users
        ];

        return response()->json($data);
    }
}
