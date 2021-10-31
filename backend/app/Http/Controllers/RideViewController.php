<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    /**
     * ライド管理画面を表示
     */
    public function showRideAdmin()
    {
        return view('ride.admin');
    }

    /**
     * ライド詳細画面を表示
     */
    public function showRide(Request $request)
    {
        $ride = DB::table('rides')
            ->where('uuid', $request->uuid)
            ->get('name');

        return view('ride.detail', ['ride' => $ride[0]]);
    }

    /**
     * 参加ライド一覧画面を表示
     */
    public function showMyRides()
    {
        return view('ride.participated');
    }

    /**
     * 集合場所検索・登録フォームを表示
     */
    public function showMeetingPlaceRegisterForm()
    {
        return view('meetingPlace.register');
    }

    /**
     *
     */
    public function showRegisterRideRouteForm()
    {
        return view('rideRoute.register');
    }
}
