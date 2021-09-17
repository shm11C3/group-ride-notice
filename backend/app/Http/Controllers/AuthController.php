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
        //[todo] 総当たり攻撃対策を実装

        $credentials = $request->only('email','password');
        $remember = $request['remember'];

        if (Auth::attempt($credentials, $remember)) {
            //ログイン成功時
            $request->session()->regenerate();

            return redirect()->route('showLogin');
        }

        

        return back()->withErrors(['isInvalidPassword' => 'パスワードが違います'])->withInput();
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
