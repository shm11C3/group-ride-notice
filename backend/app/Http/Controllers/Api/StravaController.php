<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use CodeToad\Strava\StravaFacade;
use Illuminate\Http\Request;
use App\Models\StravaUser;

class StravaController extends Controller
{
    public function __construct(StravaUser $stravaUser)
    {
        $this->stravaUser = $stravaUser;
    }

    /**
     * @param  int         $page   ページ
     * @return object json $result 取得したルート
     *
     * STRAVAで作成したルートを取得
     */
    public function getUserRoute(int $page)
    {
        $stravaUser = $this->stravaUser->getStravaUser();

        if(!$stravaUser){
            return response()->json(['result' => null]);
        }
        $result = StravaFacade::athleteRoutes($stravaUser->access_token, $stravaUser->strava_id, $page, $perPage=15);

        return response()->json($result);
    }

    /**
     * Stravaのパワーデータを取得
     *
     * @var bool $byPower
     * @var float $userStrength
     *
     * @return object $data
     */
    public function getUserStrength()
    {
        $stravaUser = $this->stravaUser->getStravaUser();

        if($stravaUser->strava_id ?? false){
            $athlete = StravaFacade::athlete($stravaUser->access_token); // Stravaから連携したアスリートのデータを取得
            $userStrength = $this->stravaUser->getPowerWeightRatio($athlete); //ユーザのPWRを取得
        }else{
            $userStrength = 0.0;
        }

        if($userStrength){
            $byPower = true;
        }else{
            $byPower = false;
        }

        $data = [
            'strength' => $userStrength,
            'byPower'  => $byPower
        ];

        return response()->json($data);
    }
}
