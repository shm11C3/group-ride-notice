<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_by', 'uuid');
    }

    public function isFollowed(string $user_by, string $user_to){
        $isFollowed = DB::table('follows')
            ->where('user_by', $user_by)
            ->where('user_to', $user_to)
            ->exists();

        return $isFollowed;
    }

    /**
     * $user_uuid のフォロワーを返す
     *
     * @param string $user_uuid
     * @return object
     */
    public function getFollowersBy_user_uuid(string $user_uuid)
    {
        return DB::table('follows')
            ->where('user_to', $user_uuid)
            ->get([
                'user_by'
            ]);
    }

    /**
     * $user_uuid のフォローを返す
     *
     * @param string $user_uuid
     * @return object
     */
    public function getFollowsBy_user_uuid(string $user_uuid)
    {
        return DB::table('follows')
            ->where('user_by', $user_uuid)
            ->get([
                'user_to'
            ]);
    }

    /**
     * @param object $follows
     * @return array $follows_arr
     */
    public function follows_to_arr(object $follows)
    {
        $follows_arr = [];
        foreach($follows as $follow){
            array_push($follows_arr, $follow->user_to);
        }
        return $follows_arr;
    }

    /**
     * @param object $follows
     * @return array $follows_arr
     */
    public function followers_to_arr(object $followers)
    {
        $followers_arr = [];
        foreach($followers as $follower){
            array_push($followers_arr, $follower->user_by);
        }
        return $followers_arr;
    }

    /**
     * 取得したユーザにフォロワー配列を追加する
     *
     * @param object $user
     * @param object $followers
     *
     * @return void
     */
    public function pushFollower_to_user(object $user, object $followers)
    {
        $user->followers = [];
        foreach($followers as $follower){
            if($follower->user_to === $user->uuid){
                array_push($user->followers, $follower);
            }
        }
    }
}
