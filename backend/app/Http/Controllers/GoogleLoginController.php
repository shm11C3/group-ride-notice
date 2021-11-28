<?php

namespace App\Http\Controllers;

use App\Models\GoogleUser;
use App\Models\User;
use Illuminate\Http\Request;
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
     * @return string $auth_uuid
     */
    private function loginUser(object $googleUser)
    {
        $googleUserData = DB::table('google_users')->where('google_id', $googleUser->id)->get('user_uuid');

        if(!isset($googleUserData[0])){
            // google連携アカウントに一致するBipokeleアカウントが登録されていない場合
            $auth_uuid = Str::uuid();
            $user_id = $this->createUser($googleUser, $auth_uuid);

        }else{

            $auth_user = User::where('uuid', $googleUserData[0]->user_uuid)->get(['id', 'uuid']);

            if(isset($auth_user[0])){
                $auth_uuid = $auth_user[0]->uuid;
                $user_id = $auth_user[0]->id;

            }else{
                // Googleアカウントと連携済みだが、アカウントが存在しない場合
                $auth_uuid = $googleUserData[0]->user_uuid;
                $user_id = $this->createUser($googleUser, $auth_uuid);
            }
        }
        Auth::loginUsingId($user_id, $remember = true);

        return $auth_uuid;
    }

    /**
     * ユーザの新規作成
     *
     * @param object $googleUser
     * @param string $auth_uuid
     *
     * @return string $auth_uuid
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
     * @return redirect showRegisterOAuthUser
     */
    public function authGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        if(!$googleUser){
            return redirect()->route('showLogin')->withErrors($this->googleAuthError);
        }

        // ログインしていない場合、ログインまたは登録を行いユーザのuuidを取得
        $auth_uuid = Auth::user()->uuid ?? $this->loginUser($googleUser);

        GoogleUser::firstOrCreate([
            'google_id' => $googleUser->id,
        ], [
            'user_uuid' => $auth_uuid
        ]);

        return redirect()->route('showRegisterOAuthUser');
    }
}

