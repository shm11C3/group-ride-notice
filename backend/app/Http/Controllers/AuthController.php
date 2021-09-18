<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
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
     * ユーザ登録処理
     * 
     * @param RegisterRequest
     * @return redirect
     */
    public function store(RegisterRequest $request)
    {
        //[todo] 同じIPで大量のアカウントを作成できないように制限をかける

        
        DB::table('users')
            ->insert([
                'uuid' => Str::uuid(),
                'name' => $request['name'],
                'email' => $request['email'],
                'prefecture_code' => $request['prefecture_code'],
                'password' => Hash::make($request['password']),
            ]);

        
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
        $credentials = $request->only('email','password');
        $remember = $request['remember'];
        $user =  User::where('email', '=', $credentials['email'])->first();

        if(!$user){
            return back()->withErrors(['email' => 'このメールアドレスは登録されていません']);
        }

        if($this->user->isAccountLocked($user)){
            return back()->withInput()->withErrors([ 'account_lock' => 'アカウントはロックされています']);
        }

        if (Auth::attempt($credentials, $remember)) {
            //ログイン成功時
            if ($user->error_count > 0) {
                $this->user->unlockAccount($user);
            }

            $request->session()->regenerate();

            return redirect()->route('showLogin');
        }

        //ログイン失敗時 [todo]ログイン試行クライアントIPとロックユーザからログインを制限する
        $error_countLimit = 6;

        $error_count = $user->error_count++;

        if($error_count < $error_countLimit){
            //ロック回数 未到達時
            if($error_count < 3){

                $user->save();
                return back()->withErrors(['isInvalidPassword' => 'パスワードが違います'])->withInput();
            }else{

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
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('showDashboard');
    }
}
