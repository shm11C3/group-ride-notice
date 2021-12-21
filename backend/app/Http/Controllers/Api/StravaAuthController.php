<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StravaUser;
use CodeToad\Strava\Strava;
use CodeToad\Strava\StravaFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StravaAuthController extends Controller
{
    public function __construct(StravaUser $stravaUser)
    {
        $this->stravaUser = $stravaUser;
    }

    /**
     * STRAVA OAuthの認証画面にリダイレクト
     *
     * @param void
     * @return redirect 'https://www.strava.com/oauth/authorize'
     */
    public function stravaAuth()
    {
        return StravaFacade::authenticate($scope='read_all,profile:read_all,activity:read_all');
    }

    /**
     * OAuth認可時に取得したコードから必要なデータを取得し、DBに格納
     * @see https://developers.strava.com/docs/getting-started/
     *
     * @param Request $request
     * @return
     */
    public function authStravaCallback(Request $request)
    {
        $user = Auth::user();

        $token = $this->getToken($request->code);

        if(!$user){
            // ログインしていない場合新規登録する
            $user = $this->registerUser($token);
        }

        DB::table('strava_users')
            ->updateOrInsert([
                'user_uuid'     => $user->uuid,
                'strava_id'     => $token->athlete->id,
                'expires_at'    => $token->expires_at,
                'refresh_token' => $token->refresh_token,
                'access_token'  => $token->access_token,
            ]);

        $athlete = StravaFacade::athlete($token->access_token);

        $powerWeightRatio = $this->stravaUser->getPowerWeightRatio($athlete); //ユーザのPWRを取得

        //[todo] ユーザのパワーデータ(PWR, FTP)テーブルを作成しレベル分けできる処理を実装。データ非公開ユーザ、パワーメータ非所持者のパターンも考慮

        if($powerWeightRatio){
            // データが存在する場合にはDBに非公開フラグと挿入

        }

        return redirect()->route('showRegisterOAuthUser');
    }

    /**
     * 認可サーバからアクセストークンを取得
     * call GET https://www.strava.com/api/v3/athlete
     *
     * @see https://developers.strava.com/docs/authentication/
     *
     * @param string $code トークン生成に用いるコード
     * @return object {
     *                  "token_type"   : "Bearer",
     *                  "expires_at"   : int,      提供されたアクセストークンが失効するUNIX時間
     *                  "expires_in"   : int,      アクセストークンが失効するまでの秒数(6時間)
     *                  "refresh_token": string,   access_token を更新するためのトークン
     *                  "access_token" : string,   APIのアクセストークン
     *                  "athlete"      : object,   ユーザのデータ
     *                }
     */
    private function getToken(string $code)
    {
       return StravaFacade::token($code);
    }

    /**
     *
     * @return $user
     */
    private function registerUser(object $token)
    {
        $auth_uuid = Str::uuid();
        DB::beginTransaction();
        try{

                DB::table('user_profiles')
                    ->insert([
                        'user_uuid' => $auth_uuid,
                        'user_intro' => $token->athlete->bio,
                        'user_url' => '',
                        'user_profile_img_path' => $token->athlete->profile,
                    ]);

                $user_id = DB::table('users')
                    ->insertGetId([
                        'uuid' => $auth_uuid,
                        'name' => $token->athlete->firstname+''+$token->athlete->lastname,
                        'prefecture_code' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

            DB::commit();
        }catch(\Throwable $e){
            DB::rollback();
            abort(500);
        }

        Auth::loginUsingId($user_id);

        $user = Auth::user();

        return $user;
    }
}
