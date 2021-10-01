<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RideViewController extends Controller
{
    /**
     * トップページを表示
     */
    public function showHome()
    {
        return view('home');
    }

    /**
     * ライド登録フォームを表示
     */
    public function showRideForm()
    {
        return view('ride.createRideForm');
    }
}
