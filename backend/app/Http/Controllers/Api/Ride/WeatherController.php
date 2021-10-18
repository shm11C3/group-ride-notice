<?php

namespace App\Http\Controllers\Api\Ride;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    //地方別に分かれている道県の庁舎所在地の地方コード
    private $region_code_arr = [1 => '016000', 46 => '460100', 47 => 471000];

    /**
     * 気象庁APIを叩いて天気を取得
     * 
     * @param int $option
     * @return response
     */
    public function getWeather(int $option)
    {
        $region_code = $this->region_code_arr[$option] ?? $option.'0000';

        $response = Http::get('https://www.jma.go.jp/bosai/forecast/data/forecast/'.$region_code.'.json');        

        $data = $response->json();

        return response()->json($data);
    }   
}
