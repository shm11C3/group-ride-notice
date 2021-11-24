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
    public function getGoogleAuth()
    {
        return Socialite::driver('google')
            ->redirect();
    }

    /**
     * Google認証成功時の処理
     *
     * @param Request
     * @return redirect
     */
    public function authGoogleCallback(Request $request)
    {
        $googleUser = Socialite::driver('google')->user();

        $auth_uuid = Auth::user()->uuid ?? 0;

        // 認証済みでない場合にユーザを新規作成する
        if(!$auth_uuid){
            $auth_uuid = $this->createUser($googleUser);

            // [todo] uuidを用いた認証処理を実装
        }

        GoogleUser::firstOrCreate([
            'google_id' => $googleUser->id,
        ], [
            'user_uuid' => $auth_uuid
        ]);

        // [todo] 本登録処理を実装
        return redirect()->route('showRegisterOAuthUser');
    }


    /**
     * ユーザの新規作成
     *
     * @param object $googleUser
     * @return string $auth_uuid
     */
    private function createUser(object $googleUser)
    {
        $auth_uuid = Str::uuid();
        DB::beginTransaction();
        try{

                DB::table('user_profiles')
                    ->insert([
                        'user_uuid' => $auth_uuid,
                        'user_intro' => '',
                        'user_url' => '',
                        'user_profile_img_path' => $googleUser->avatar
                    ]);

                DB::table('users')
                    ->insert([
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

        return $auth_uuid;
    }
}

