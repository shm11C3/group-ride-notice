<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserViewController extends Controller
{
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * ユーザーの設定画面を表示
     */
    public function showConfig()
    {
        return view('user.config');
    }

    /**
     * ユーザーのプロフィールページを表示
     *
     * @param string $user_uuid
     * @return view 'user/{user_uuid}'
     */
    public function showUser(string $user_uuid)
    {
        $user = User::with('followers.user')
            ->where('uuid', $user_uuid)
            ->join('user_profiles', 'user_profiles.user_uuid', 'users.uuid')
            ->get([
                'users.uuid',
                'name',
                'prefecture_code',
                'user_profile_img_path',
                'user_intro',
                'user_url',
                'fb_username',
                'tw_username',
                'ig_username',
            ]);

        if(!isset($user[0])){
            return abort(404);
        }
        $auth_user = Auth::user()->uuid ?? 0;
        $follower_isExist = !empty($user[0]->followers[0]);

        if($follower_isExist || $auth_user){
            // フォロー済みの場合True
            $userFollowed = $this->user->getUserFollowed($user[0]->followers, $auth_user);

        }else{
            $userFollowed = false;
        }

        $prefecture =  $this->user->prefecture_arr[$user[0]->prefecture_code]; // 都道府県名を取得
        if(!$user[0]->user_profile_img_path){
            $user[0]->user_profile_img_path = asset($this->user->userDefaultImgPath[100]);
        }
        return view('user.profile', ['user' => $user[0], 'prefecture' => $prefecture, 'userFollowed' => $userFollowed]);
    }
}
