<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RideViewController extends Controller
{
    /**
     * ライド登録フォームを表示
     */
    public function showRideForm()
    {
        return view('ride.createRideForm');
    }
}
