<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Http\Requests\UploadUserProfileImageRequest;
use App\Models\User;
use App\Models\UserProfile;

class UserProfileController extends Controller
{
    public function __construct(UserProfile $userProfile, User $user)
    {
        $this->userProfile = $userProfile;
        $this->user = $user;
    }

    /**
     * ユーザープロフィールを更新
     *
     * @param App\Http\Requests\UpdateUserProfileRequest
     * @return response
     */
    public function updateUserProfile(UpdateUserProfileRequest $request)
    {
        $user_uuid = Auth::user()->uuid;

        DB::table('user_profiles')
            ->where('user_uuid', $user_uuid)
            ->update([
                'user_intro' => $request['user_intro'],
                'user_url' => $request['user_url'],
                'fb_username' => $request['fb_username'],
                'tw_username' => $request['tw_username'],
                'ig_username' => $request['ig_username'],
                'updated_at' => now(),
            ]);

        DB::table('users')
            ->where('uuid', $user_uuid)
            ->update([
                'name' => $request['name'],
                'prefecture_code' => $request['prefecture_code'],
                'updated_at' => now()
            ]);

        $data = ['status' => true];

        return response()->json($data);
    }

    /**
     * users.uuidから機密でないユーザーの情報を返す
     *
     * @param string $user_uuid
     * @return response $user
     */
    public function getUserProfile(string $user_uuid)
    {

        $user = DB::table('users')
        ->where('users.uuid', $user_uuid)
        ->join('user_profiles', 'user_uuid', 'users.uuid')
        ->get([
            'name',
            'prefecture_code',
            'user_profile_img_path',
            'user_intro',
            'user_url',
            'fb_username',
            'tw_username',
            'ig_username'
        ]);

        if(!$user[0]->user_profile_img_path){
            $user[0]->user_profile_img_path = asset($this->user->userDefaultImgPath[100]);
        }

        return response()->json($user);
    }

    /**
     * 認証ユーザー自身の情報を返す
     *
     * @param string $user_uuid
     * @return response $user
     */
    public function getAuthUserProfile()
    {
        $user_uuid = Auth::user()->uuid;

        $user = DB::table('users')
        ->where('users.uuid', $user_uuid)
        ->join('user_profiles', 'user_uuid', 'users.uuid')
        ->leftJoin('google_users', 'google_users.user_uuid', 'users.uuid')
        ->get([
            'google_users.user_uuid as google_user',
            'name',
            'prefecture_code',
            'email',
            'users.created_at',
            'user_profile_img_path',
            'user_intro',
            'user_url',
            'fb_username',
            'tw_username',
            'ig_username',
        ]);

        if(!$user[0]->user_profile_img_path){
            $user[0]->user_profile_img_path = asset($this->user->userDefaultImgPath[100]);
        }

        return response()->json($user);
    }

    /**
     * ユーザが投稿したプロフィール画像ファイルをS3へアップロード
     *
     * @param App\Http\Requests\UploadUserProfileImageRequest
     * @return object $status
     */
    public function uploadUserProfileImg(UploadUserProfileImageRequest $request)
    {
        $user_uuid = Auth::user()->uuid;
        $img_url = $this->userProfile->putUserImage($request->file('user_profile_img'), 'public');

        DB::table('user_profiles')
        ->where('user_uuid', $user_uuid)
        ->update([
            'user_profile_img_path' => $img_url
        ]);

        return response()->json(['img_url' => $img_url]);
    }
}
