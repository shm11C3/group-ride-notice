<?php

namespace App\Models;

use Carbon\Carbon;
use CodeToad\Strava\StravaFacade;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
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
        if(property_exists($athlete, 'ftp') && property_exists($athlete, 'weight')){
            // weightの単位はkgだが今後のAPI仕様次第でヤードポンド法にも対応させる必要あり
            return $this->floor_plus($athlete->ftp / $athlete->weight);
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

    /**
     * アクセストークンを取得
     *
     * @var object $refresh 更新後のトークン
     *
     * @param void
     * @return object $stravaUser ユーザデータとトークン
     */
    public function getStravaUser()
    {
        $stravaUser = Auth::user()->stravaUser;

        // アクセストークンの更新が必要ない場合はそのまま値を帰す
        if(!$stravaUser || strtotime(Carbon::now()) < $stravaUser->expires_at){
            return $stravaUser;
        }

        $refresh = StravaFacade::refreshToken($stravaUser->refresh_token); // トークンの更新

        DB::table('strava_users')
            ->where('user_uuid', $stravaUser->user_uuid)
            ->update([
                'access_token' => $refresh->access_token,
                'refresh_token' => $refresh->refresh_token,
                'expires_at' => $refresh->expires_at,
            ]);

        return Auth::user()->stravaUser;
    }

    /**
     * 桁数を指定して切り捨て
     *
     * @param float $value     切り捨てる値
     * @param int   $precision 切り捨てる桁数
     */
    function floor_plus(float $value, ?int $precision = null): float
    {
      if (null === $precision) {
        return (float)floor($value);
      }
      if ($precision < 0) {
        throw new \RuntimeException('Invalid precision');
      }

      $reg = $value - 0.5 / (10 ** $precision);
      return round($reg, $precision, $reg > 0 ? PHP_ROUND_HALF_UP : PHP_ROUND_HALF_DOWN);
    }
}
