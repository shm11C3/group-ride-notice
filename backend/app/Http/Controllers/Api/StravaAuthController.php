<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StravaUser;
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
     * STRAVA OAuthの許可画面にリダイレクト
     *
     * @param void
     * @return redirect 'https://www.strava.com/oauth/authorize'
     */
    public function stravaAuth()
    {
        return StravaFacade::authenticate($scope='read_all,profile:read_all,activity:read_all');
    }

    /**
     * 連携許可時の処理
     * 取得したAuth認可コードを用いてbipokeleのアカウントと連携させる
     * STRAVAアカウントの登録は１Bipokeleアカウントあたり１STRAVAアカウント
     *
     * @see https://developers.strava.com/docs/getting-started/
     *
     * @param  Request $request
     * @return Response
     */
    public function authStravaCallback(Request $request)
    {
        if(!$request->code){
            // strava認証キャンセル時にリダイレクトさせる
            return redirect()->route('showLogin');
        }
        $stravaUserToken = $this->getToken($request->code); //stravaのトークン・アスリートデータ
        $user_id = $this->stravaUser->getUserIdByStravaId($stravaUserToken->athlete->id); // (int)strava_idと一致するbipokeleアカウント(int)idを取得
        $is_registered = true; // ユーザアカウントの有無

        if(!Auth::check()){
            // ログインしていない場合はログインする
            if(!$user_id){
                // 連携しているbipokeleアカウントが存在しない場合は新規作成する
                $user_id = $this->registerUser($stravaUserToken);
                $is_registered = false;
            }
            Auth::loginUsingId($user_id, $remember=true);

        }else if($user_id){
            // ログイン済みかつstravaアカウントとbipokeleアカウントがすでに紐付いていた場合エラー画面へリターン
            return redirect()->route('showOAuthUserAlreadyRegistered');
        }

        $user = Auth::user()->stravaUser ?? Auth::user();

        $tokenData = [
            'expires_at'    => $stravaUserToken->expires_at,
            'refresh_token' => $stravaUserToken->refresh_token,
            'access_token'  => $stravaUserToken->access_token,
        ];

        if(isset($user->strava_id) && (int)$user->strava_id === $stravaUserToken->athlete->id){
            // `strava_users`に登録されている場合
            DB::table('strava_users')
                ->where('strava_id', $stravaUserToken->athlete->id)
                ->update(array_merge(
                    ['user_uuid' => $user->user_uuid],
                    $tokenData,
                ));

        }else if(!isset($user->strava_id)){
            // STRAVAアカウント新規登録時
            DB::table('strava_users')
                ->insert(array_merge(
                    ['user_uuid' => $user->uuid,
                     'strava_id' => $stravaUserToken->athlete->id],
                    $tokenData,
                ));
        }else{
            // 登録されている`strava_id`とレスポンスから取得したidが異なる場合
            DB::table('strava_users')
                ->where('user_uuid', $user->user_uuid)
                ->update(array_merge(
                    ['strava_id' => $stravaUserToken->athlete->id],
                    $tokenData,
                ));
        }

        if($is_registered){
            // 新規登録以外の場合
            return redirect()->route('showDashboard');
        }

        // 新規登録の場合（新規登録画面へリダイレクト）
        $user_data = [
            'uuid'                  => $user->uuid,
            'name'                  => $user->name,
            'user_profile_img_path' => $stravaUserToken->athlete->profile,
        ];

        return redirect()->route('showRegisterOAuthUser', $user_data);
    }

    /**
     * 認可サーバからアクセストークンを取得
     * call GET https://www.strava.com/api/v3/athlete
     *
     * @see https://developers.strava.com/docs/authentication/
     *
     * @param string $code OAuth認可コード
     * @return object {
     *     string "token_type"    : "Bearer",
     *     int    "expires_at"    : 提供されたアクセストークンが失効するUNIX時間,
     *     int    "expires_in"    : アクセストークンが失効するまでの秒数(6時間),
     *     string "refresh_token" : access_token を更新するためのトークン,
     *     string "access_token"  : APIのアクセストークン,
     *     object "athlete"       : ユーザのデータ,
     *                }
     */
    private function getToken(string $code)
    {
       return StravaFacade::token($code);
    }

    /**
     * ユーザの新規登録
     *
     * @param  object $stravaUserToken
     * @return int    $user_id
     */
    private function registerUser(object $stravaUserToken)
    {
        $auth_uuid = Str::uuid();
        DB::beginTransaction();
        try{
            DB::table('user_profiles')
                ->insert([
                    'user_uuid'             => $auth_uuid,
                    'user_intro'            => $stravaUserToken->athlete->bio,
                    'user_url'              => '',
                    'user_profile_img_path' => $stravaUserToken->athlete->profile,
                ]);

                $user_id = DB::table('users')
                ->insertGetId([
                    'uuid'            => $auth_uuid,
                    'name'            => $stravaUserToken->athlete->firstname.' '.$stravaUserToken->athlete->lastname,
                    'prefecture_code' => 0,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

            DB::commit();
        }catch(\Throwable $e){
            DB::rollback();
            abort(500);
        }

        return $user_id;
    }
}
