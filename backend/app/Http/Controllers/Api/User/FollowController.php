<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterFollowRequest;
use App\Models\Follow;
use Illuminate\Support\Str;

class FollowController extends Controller
{
    public function __construct(Follow $follow)
    {
        $this->follow = $follow;
    }

    /**
     * フォロー処理
     * すでにフォローされている場合はフォローを解除する
     *
     * @param App\Http\Requests\RegisterFollowRequest
     * @return response
     */
    public function follow(RegisterFollowRequest $request)
    {
        $auth = Auth::user();
        $user_to = $request['user_to'];

        if($auth->uuid == $user_to){
            // フォロー先ユーザが現在のログインユーザの場合
            $data = ['status' => 'invalid'];
            return response()->json($data);
        }

        if($this->follow->isFollowed($auth->uuid, $user_to)){
            // フォロー済みの場合
            DB::table('follows')
                ->where('user_by', $auth->uuid)
                ->where('user_to', $user_to)
                ->delete();

            $data = ['status' => 'unfollow', 'follow' => false];

            return response()->json($data);

        }else{
            // フォロー外の場合
            $uuid = Str::uuid();

            DB::table('follows')
                ->insert([
                    'uuid' => $uuid,
                    'user_to' => $user_to,
                    'user_by' => $auth->uuid,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            $data = ['status' => 'follow', 'follow' => true];

            return response()->json($data);
        }
    }

    /**
     * $user_by がフォローしているユーザを取得
     *
     * @param string $user_by
     * @return Response
     */
    public function getFollows(string $user_by)
    {
        $follows = DB::table('follows')
            ->where('user_by', $user_by)
            ->join('users', 'users.uuid', 'user_to')
            ->join('user_profiles', 'user_uuid', 'user_to')
            ->orderBy('follows.created_at', 'desc')
            ->get([
               'users.uuid',
               'name',
               'user_profile_img_path'
            ]);

        return response()->json($follows);
    }

    /**
     * $user_to をフォローしているユーザを取得
     *
     * @param string $user_to
     * @param Response
     */
    public function getFollowers(string $user_to)
    {
        $followers = DB::table('follows')
            ->where('user_to', $user_to)
            ->join('users', 'users.uuid', 'user_to')
            ->join('user_profiles', 'user_uuid', 'user_to')
            ->orderBy('follows.created_at', 'desc')
            ->get([
               'users.uuid',
               'name',
               'user_profile_img_path'
            ]);

        return response()->json($followers);
    }
}
