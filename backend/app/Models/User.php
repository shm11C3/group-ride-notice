<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public function rides()
    {
        return $this->belongsToMany(Ride::class, 'ride_participants', 'user_uuid', 'uuid');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'locked_flg',
        'locked_at',
        'error_count',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $prefecture_arr = [
        '',
        '北海道',
        '青森県',
        '岩手県',
        '宮城県',
        '秋田県',
        '山形県',
        '福島県',
        '茨城県',
        '栃木県',
        '群馬県',
        '埼玉県',
        '千葉県',
        '東京都',
        '神奈川県',
        '新潟県',
        '富山県',
        '石川県',
        '福井県',
        '山梨県',
        '長野県',
        '岐阜県',
        '静岡県',
        '愛知県',
        '三重県',
        '滋賀県',
        '京都府',
        '大阪府',
        '兵庫県',
        '奈良県',
        '和歌山県',
        '鳥取県',
        '島根県',
        '岡山県',
        '広島県',
        '山口県',
        '徳島県',
        '香川県',
        '愛媛県',
        '高知県',
        '福岡県',
        '佐賀県',
        '長崎県',
        '熊本県',
        '大分県',
        '宮崎県',
        '鹿児島県',
        '沖縄県'
    ];
    
    /**
     * ロック時間算出
     * ロック時間は32秒から2の累乗で増加
     * 
     * @param int $locked_flg
     * @return int $lockTime
     */
    public function lockTime($locked_flg)
    {
        $a = $locked_flg + 4;
        $lockTime = 2 ** $a;

        return $lockTime;
    }

    /**
     * ロック時間からの経過時間算出
     * 
     * @param string $locked_at
     * @return int
     */
    public function lockLaterTime($locked_at)
    {
        $now = strtotime(now());
        $locked_at = strtotime($locked_at) ?? $now;

        return $now - $locked_at;
    }

    /**
     * アカウントロックチェック
     * 
     * @param object $user
     * @return bool
     */
    public function isAccountLocked($user)
    {
        $lockLaterTime = $this->lockLaterTime($user->locked_at);
        $lockTime = $this->lockTime($user->locked_flg);

        if ($user->locked_flg > 0 && $lockTime > $lockLaterTime) {
            
            return true; 
        }

        return false;
    }

    /**
     * 残りロック時間算出
     * 
     * @param int $lockTime, $timeCount
     * @return int
     */
    public function timeLeft($lockTime, $lockLaterTime)
    {
        return $lockTime - $lockLaterTime;
    }

    /**
     * 秒数を$hours時間$minutes分$seconds秒に変換
     * 
     * @param int $sec
     * @return string
     */
    public function secToTime($sec)
    {
        $hours = floor( $sec / 3600 );
        $minutes = floor( ( $sec / 60 ) % 60 );
        $seconds = $sec % 60;

        if($hours == 0){
            return  $minutes.'分'.$seconds.'秒間';
        }elseif($hours + $minutes == 0){
            return $seconds.'秒間';
        }
        return $hours.'時間'.$minutes.'分'.$seconds.'秒間';
    }

    /**
     * ユーザーロックデータをリセット
     * 
     * @param $user
     */
    public function unlockAccount($user)
    {
        $user->locked_at = null;
        $user->error_count = 0;
        $user->locked_flg = 0;
        $user->save();
    }

    /**
     * アカウントロック
     * 
     * @param $user
     */
    public function lockAccount($user)
    {
        $user->locked_flg++;
        $user->locked_at = now();
        $user->save();
    }

    /**
     * IPアドレスを記録
     */
    public function recordIp(string $uuid, string $ip, bool $status)
    {
        DB::table('auth_request_ip_address')
        ->insert([
            'user_uuid' => $uuid,
            'user_ip' => $ip,
            'request_status' => $status
        ]);
    }
}
