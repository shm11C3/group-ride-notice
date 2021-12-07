<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UserProfile extends Model
{
    use HasFactory;

    /**
     * ユーザプロフィール画像をS3へアップロード
     *
     * @todo 画像のバリデーション、圧縮処理を実装
     *
     * @param object $img
     * @return string img_url
     */
    public function putUserImage(object $img)
    {
        $path = Storage::disk('s3')->putFile('/img/user-profile', $img);
        Storage::disk('s3')->setVisibility($path, 'public');
        return Storage::disk('s3')->url($path);
    }
}
