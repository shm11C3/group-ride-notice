<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class UserProfile extends Model
{
    use HasFactory;

    private $initial_image_name = 'cropped.png';

    /**
     * ユーザプロフィール画像をS3へアップロード
     *
     * @param object  $img
     * @return string img_url
     */
    public function putUserImage(string $path)
    {
        $path = Storage::disk('s3')->putFile('img/user_profiles', $path, 'public');
        return Storage::disk('s3')->url($path);
    }

    /**
     * ファイル名がフロント側で生成した初期値かチェック
     *
     * @param string $filename
     * @return bool
     */
    public function isRegularFilename(string $filename)
    {
        return ($filename === $this->initial_image_name);
    }

    /**
     * 画像を最適化して書き出す
     *
     * @param object  $param_img
     * @return string $path
     */
    public function encodeImage(object $original_img)
    {
        $uuid = (string)Str::uuid();
        $path = "img/tmp/{$uuid}.jpg"; // 書き出し後の画像の一時パス

        $img = Image::make($original_img)
            ->encode('jpg');

        $quality = $this->getQuality($img->filesize());

        $img->save($path, $quality);
        $img->destroy();

        return $path;
    }

    /**
     * 適切な画像品質を取得
     *
     * @param int  $img_size_bite 許容される最大値:1024KB
     * @return int $quality       0 ~ 87
     */
    private function getQuality(int $img_size_bite)
    {
        $quality = 25000000 / $img_size_bite; // 20KB ~ 60KBに収束するように調整

        if($quality >= 87){
            return 87;
        }else if($quality <= 0){
            return 0;
        }

        return (int) $quality;
    }

    /**
     * URIがBipokeleのS3バケットのものであるかの真偽値を返す
     *
     * @param string $uri
     * @return bool
     */
    public function isBipokeleStorageUri($uri)
    {
        return (explode('/', $uri)[3]??'' === 'bipokele-app');
    }
}
