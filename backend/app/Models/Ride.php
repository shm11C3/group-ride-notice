<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Ride extends Model
{
    use HasFactory;

     /**
     * $user_idとログインユーザの一致チェック
     * 
     * @param string $user_id
     * @return bool
     */
    public function isAuthUser($user_id)
    {
        $loginUser_id = Auth::user()->uuid ?? '';

        if($user_id === $loginUser_id) {
            return true;
        }
        return false;
    }

    /**
     * 都道府県コードからSQLの演算子を返す
     * 
     * @param int $code
     * @return string
     */
    public function getOpeByCode($code)
    {
        if($code<1 || $code>47){
            return '!=';
        }
        return '=';
    }

    /**
     * 引数からsqlに用いる時間の最小値と最大値の配列を返す
     * 
     * @param int $time_appoint
     * @return array $result
     */
    public function createTimeSql($time_appoint)
    {
        $now = date('Y-m-d H:i:s', strtotime('now'));

        //時間の最小値
        $requestArr_min = [
            $now,                                             //すべて
            $now,                                             //今日中
            date('Y-m-d', strtotime('now 1day')).' 00:00:00', //明日中
            date('Y-m-d', strtotime('now 2day')).' 00:00:00', //1週間以内
            date('Y-m-d', strtotime('now 8day')).' 00:00:00', //1ヶ月以内
            date('2021-06-26 19:30:00')                       //過去全て
        ];

        //時間の最小値
        $requestArr_max = [
            date('Y-m-d', strtotime('now 2year')).' 23:59:59',  //すべて
            date('Y-m-d', strtotime('now')).' 23:59:59',        //今日中
            date('Y-m-d', strtotime('now 1day')).' 23:59:59',   //明日中
            date('Y-m-d', strtotime('now 1week')).' 23:59:59',  //1週間以内
            date('Y-m', strtotime('now 1month')).'-01 23:59:59', //1ヶ月以内
            $now
        ];


        if($time_appoint >= count($requestArr_min)){
            //存在しないキーの場合0を代入
            $time_appoint = 0;
        }


        $result = [
            $requestArr_min[$time_appoint],
            $requestArr_max[$time_appoint]
        ];

        return $result;
    }

    /**
     * 引数からsqlに用いる強度の最小値と最大値の配列を返す
     * 
     * @param int $intstRange
     * @return array $result
     */
    public function getIntstByRange($intstRange)
    {
        $intst_min = [
            0, 0, 2, 4, 7
        ];

        $intst_max = [
            10, 1, 3, 6, 10,
        ];


        if($intstRange >= count($intst_min)){
            //存在しないキーの場合0を代入
            $intstRange = 0;
        }


        $result = [
            $intst_min[$intstRange],
            $intst_max[$intstRange]
        ];

        return $result;
    }
}