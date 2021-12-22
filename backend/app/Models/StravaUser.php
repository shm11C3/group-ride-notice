<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StravaUser extends Model
{
    use HasFactory;

    /**
     * PWRを算出
     * データがない場合は値を返さない
     *
     * @param object $athlete
     *
     * @return float
     * @return void
     */
    public function getPowerWeightRatio(object $athlete)
    {
        if($athlete->ftp && $athlete->weight){
            // weightの単位はkgだが今後のAPI仕様次第でヤードポンド法にも対応させる必要あり
            return $athlete->ftp / $athlete->weight;
        }
    }

    /**
     * strava_idから連携しているユーザidを返す
     *
     * @param  int $strava_id stravaアカウントのid
     * @return int $user_id   bipokeleアカウントのid
     */
    public function getUserIdByStravaId(int $strava_id)
    {
        $user_id = DB::table('strava_users')
            ->join('users', 'users.uuid', 'user_uuid')
            ->where('strava_id', $strava_id)
            ->get('users.id');

        return $user_id[0]->id ?? null;
    }
}
