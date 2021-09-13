<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthController extends Controller
{
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
        
        return redirect(route('dashboard'));
    }
}
