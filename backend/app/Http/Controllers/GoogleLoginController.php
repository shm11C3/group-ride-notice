<?php

namespace App\Http\Controllers;

use App\Models\GoogleUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    private $googleAuthError = ['googleAuthError' => '予期せぬエラーが発生しました。'];

    /**
     * Google認証画面にリダイレクト
     *
     * @param void
     * @return redirect
     */
    public function getGoogleAuth()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * ユーザがログインしていない場合の処理
     *
     * @param object $googleUser
     * @return array $auth_user_arr
     */
    private function loginOrRegisterUser(object $googleUser)
    {
        $googleUserData = DB::table('google_users')->where('google_id', $googleUser->id)->get('user_uuid');
        $registered = true;

        $auth_user = [];

        if(!isset($googleUserData[0])){
            // google連携アカウントに一致するBipokeleアカウントが登録されていない場合
            $auth_uuid = Str::uuid();
            $auth_id = $this->createUser($googleUser, $auth_uuid);

            $registered = false;

        }else{
            // 登録済みのアカウントに紐付ける場合
            $auth_uuid = $googleUserData[0]->user_uuid;

            $auth_user = User::where('uuid', $googleUserData[0]->user_uuid)->get(['id', 'prefecture_code']); // Google登録テーブルのuuidからユーザを取得

            // 登録ユーザのidを取得、存在しない場合はユーザを登録
            if($auth_user[0]->id){
                $auth_id = $auth_user[0]->id;
            }else{
                $auth_id = $this->createUser($googleUser, $auth_uuid);
                $registered = false;
            }
        }

        $auth_user_arr = [
            'id' => $auth_id,
            'uuid' => $auth_uuid,
            'registered' => $registered
        ];

        Auth::loginUsingId($auth_user_arr['id'], $remember = true);

        return $auth_user_arr;
    }

    /**
     * ユーザの新規作成
     *
     * @param object $googleUser
     * @param string $auth_uuid
     *
     * @return string $user_id
     */
    private function createUser(object $googleUser, string $auth_uuid)
    {
        DB::beginTransaction();
        try{

                DB::table('user_profiles')
                    ->insert([
                        'user_uuid' => $auth_uuid,
                        'user_intro' => '',
                        'user_url' => '',
                        'user_profile_img_path' => $googleUser->avatar
                    ]);

                $user_id = DB::table('users')
                    ->insertGetId([
                        'uuid' => $auth_uuid,
                        'name' => $googleUser->nickname ?? $googleUser->name,
                        'prefecture_code' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

            DB::commit();
        }catch(\Throwable $e){
            DB::rollback();
            abort(500);
        }

        return $user_id;
    }

    /**
     * Google認証成功時の処理
     *
     * ログイン済みのユーザはgoogle連携のみ行いダッシュボードへ戻す
     * ログインしていないが、一度同じgoogleアカウントで認証をしているユーザはそのアカウントでログインしてダッシュボードへ戻す
     * Bipokeleログインをしておらずgoogle認証にも登録されていないユーザは連携した上でBipokeleアカウント新規作成
     *
     * @param void
     *
     * @return redirect showRegisterOAuthUser 新規登録の場合
     * @return redirect showDashboard         登録済みの場合
     */
    public function authGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user(); // GoogleAPIからユーザを取得

        if(!$googleUser){
            // APIからデータが取得できなかった場合
            return redirect()->route('showLogin')->withErrors($this->googleAuthError);
        }
        $is_registered = true; //ユーザアカウントの有無

        // ログインしていない場合、ログインまたは登録を行いユーザのuuidを取得
        $user = Auth::user();
        if($user){
            // ログイン時
            // すでに連携済みのGoogleアカウントだった場合エラー画面にリターンする
            $google_user_result = DB::table('google_users')->where('google_id', $googleUser->id)->limit(1)->get('id');
            if(isset($google_user_result[0])){
                return redirect()->route('showOAuthUserAlreadyRegistered');
            }

            $auth_user = [
                'id' => $user->id,
                'uuid' => $user->uuid,
            ];

            GoogleUser::create([
                'google_id' => $googleUser->id,
                'user_uuid' => $user->uuid
            ]);
        }else{
            // ログインしていない場合、ログイン・登録処理を行う
            $auth_user = $this->loginOrRegisterUser($googleUser);
            $is_registered = $auth_user['registered'];

            GoogleUser::firstOrCreate([
                'google_id' => $googleUser->id,
            ], [
                'user_uuid' => $auth_user['uuid']
            ]);
        }

        if($is_registered){
            // 新規登録でない場合はダッシュボードへリダイレクト
            return redirect()->route('showDashboard');
        }

        $user_data = [
            'uuid' => (string) $auth_user['uuid'],
            'name' => $googleUser->nickname ?? $googleUser->name,
            'user_profile_img_path' => $googleUser->avatar,
            'user_strength' => NULL,
        ];

        return redirect()->route('showRegisterOAuthUser', $user_data); //新規登録の場合
    }
}

