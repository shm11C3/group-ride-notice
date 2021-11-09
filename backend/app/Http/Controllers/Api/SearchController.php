<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Search;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Ride;

class SearchController extends Controller
{
    public function __construct(Search $search, User $user)
    {
        $this->search = $search;
        $this->user = $user;
    }

    /**
     * 検索用API
     *
     * @param string $request
     * @return response
     */
    public function search(string $request)
    {
        $searchWordArr = $this->search->spaceSubstitute($request); //検索キーワード配列


        $query_rides = Ride::with('rideParticipants.user')
            ->join('meeting_places', 'meeting_places.uuid', 'meeting_places_uuid')
            ->join('ride_routes', 'ride_routes.uuid', 'ride_routes_uuid')
            ->join('users', 'host_user_uuid', 'users.uuid');

        $query_users = User::with('followers.user')
            ->join('user_profiles', 'user_uuid', 'users.uuid');


        foreach($searchWordArr as $value){
            $searchWord = $this->search->escapeMetaCharacters_byStr($value);

            $query_rides->where('rides.name', 'LIKE', $searchWord)
            ->where('rides.publish_status', 0)
            ->orWhere('rides.comment', 'LIKE', $searchWord)
            ->orWhere('meeting_places.name', 'LIKE', $searchWord)
            ->orWhere('meeting_places.address', 'LIKE', $searchWord)
            ->orWhere('ride_routes.name', 'LIKE', $searchWord)
            ->orWhere('ride_routes.comment', 'LIKE', $searchWord);

            $query_users->where('name', 'LIKE', $searchWord)
            ->orWhere('user_intro', 'LIKE', $searchWord)
            ->orWhere('fb_username', 'LIKE', $searchWord)
            ->orWhere('tw_username', 'LIKE', $searchWord)
            ->orWhere('ig_username', 'LIKE', $searchWord);
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
        ->simplePaginate(20);

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
        ->simplePaginate(20);


        // 検索結果のユーザをフォローしているか
        if(isset($users[0])) {
            foreach($users as $user) {
                $user->userFollowed = false;

                if(!empty($user->followers[0])) {
                    // フォロワーが存在する場合確認を行い値を代入
                    $user->userFollowed = $this->user->getUserFollowed($user->followers, Auth::user()->uuid);
                }
            }
        }

        $data = [
            'rides' => $rides,
            'users' => $users
        ];

        return response()->json($data);
    }
}
