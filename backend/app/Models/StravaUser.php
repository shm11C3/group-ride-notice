<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
