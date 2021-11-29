<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\DeleteUserRequest;
use App\Http\Requests\RegisterOAuthUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * ユーザ登録フォームを表示
     *
     * @param void
     * @return view
     */
    public function showRegister()
    {
        return view('auth.registerForm');
    }

    /**
     * ログインフォームを表示
     */
    public function showLogin()
    {
        return view('auth.loginForm');
    }

    /**
     * パスワード更新フォームを表示
     */
    public function showUpdatePassword()
    {
        return view('auth.updatePassword');
    }

    /**
     * アカウント削除フォームを表示
     */
    public function showDeleteUser()
    {
        return view('auth.deleteUser');
    }

    /**
     * ダッシュボードを表示
     *
     * @param void
     * @return Response
     */
    public function showDashboard()
    {
        $user = DB::table('users')
            ->where('id', Auth::id())
            ->get([
                'name',
                'email',
                'prefecture_code',
                'created_at',
            ]);

        $prefecture =  $this->user->prefecture_arr[$user[0]->prefecture_code];

        return view('auth.dashboard', ['user' => $user, 'prefecture' => $prefecture]);
    }

    /**
     * OAuthユーザ登録フォームを表示
     *
     * @param object $user
     * @return view
     */
    public function showRegisterOAuthUser()
    {
        return view('auth.registerOAuthForm');
    }

    /**
     * ユーザ登録処理
     *
     * @param RegisterRequest
     * @return redirect
     */
    public function store(RegisterRequest $request)
    {
        //[todo] 同じIPで大量のアカウントを作成できないように制限をかける

        DB::beginTransaction();
        try{

            $user_uuid = Str::uuid();

            DB::table('users')
                ->insert([
                    'uuid' => $user_uuid,
                    'name' => $request['name'],
                    'email' => $request['email'],
                    'prefecture_code' => $request['prefecture_code'],
                    'password' => Hash::make($request['password']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

            DB::table('user_profiles')
                ->insert([
                    'user_uuid' => $user_uuid,
                    'user_intro' => '',
                    'user_url' => '',
                ]);

            DB::commit();
        }catch(\Throwable $e){
            DB::rollback();
            abort(500);
        }

        $this->user->recordIp($user_uuid, $request->ip(), true);

        $credentials = $request->only('email','password');
        $remember = $request['remember'];

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
        }

        return redirect()->route('showLogin');
    }

    /**
     * ログイン処理
     *
     * @param LoginRequest
     * @return redirect
     */
    public function login(LoginRequest $request)
    {
        $ip = $request->ip();
        $credentials = $request->only('email','password');
        $remember = $request['remember'];
        $user =  User::where('email', '=', $credentials['email'])->first();

        if(!$user){
            return back()->withErrors(['email' => 'このメールアドレスは登録されていません']);
        }

        if($this->user->isAccountLocked($user)){
            $this->user->recordIp($user->uuid, $ip, false);
            return back()->withInput()->withErrors([ 'account_lock' => 'アカウントはロックされています']);
        }

        if (Auth::attempt($credentials, $remember)) {
            //ログイン成功時
            if ($user->error_count > 0) {
                $this->user->unlockAccount($user);
            }

            $this->user->recordIp($user->uuid, $ip, true);
            $request->session()->regenerate();

            return redirect()->intended('showDashboard');
        }

        //ログイン失敗時 [todo]ログイン試行クライアントIPとロックユーザからログインを制限する
        $error_countLimit = 6;

        $error_count = $user->error_count++;

        if($error_count < $error_countLimit){
            //ロック回数 未到達時
            if($error_count < 3){

                $this->user->recordIp($user->uuid, $ip, false);
                $user->save();
                return back()->withErrors(['isInvalidPassword' => 'パスワードが違います'])->withInput();
            }else{
                $this->user->recordIp($user->uuid, $ip, false);
                $countDown = $error_countLimit - $error_count;
                $user->save();

                return back()
                    ->withInput()
                    ->withErrors([
                        'isInvalidPassword' => 'パスワードが違います',
                        'account_lock' => 'あと'.$countDown.'回のエラーでアカウントはロックされます',
                    ]);
            }

        }

        //ユーザーロック処理
        $this->user->lockAccount($user);
        $this->user->recordIp($user->uuid, $ip, false);

        $lockTime = $this->user->lockTime($user->locked_flg);
        $lockTime_min_sec =  $this->user->secToTime($lockTime);

        return back()
        ->withInput()
        ->withErrors([
            'isInvalidPassword' => 'パスワードが違います',
            'login_error' => 'パスワードを'.$user->error_count.'回間違えました。アカウントは'.$lockTime_min_sec.'ロックされます',
        ]);


    }

    /**
     * ユーザーをアプリケーションからログアウトさせる
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->user->recordIp(Auth::user()->uuid, $request->ip(), true);

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('showDashboard');
    }

    /**
     * パスワードを更新
     * すべての端末からログアウト
     *
     * @param App\Http\Requests\UpdatePasswordRequest
     * @return redirect
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user_uuid = Auth::user()->uuid;

        $this->user->recordIp($user_uuid, $request->ip(), true);

        $user = User::where('uuid', $user_uuid)->first();
        Auth::logoutOtherDevices($request['current_password']);

        $user->password = Hash::make($request['new_password']);
        $user->save();

        return redirect()->route('showConfig');
    }

    /**
     * アカウントを削除
     *
     * @param App\Http\Requests\DeleteUserRequest
     * @return redirect
     */
    public function deleteUser(DeleteUserRequest $request)
    {
        $user = Auth::user();

        User::where('uuid', $user->uuid)
            ->delete();

        return redirect()->route('showLogin');
    }

    public function registerOAuthUser(RegisterOAuthUserRequest $request)
    {
        dd($request);
    }
}
