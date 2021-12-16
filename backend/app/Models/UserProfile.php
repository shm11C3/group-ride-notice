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

//    private $user_img_size_min_bite = 25600;
//    private $user_img_size_max_bite = 35840;

    /**
     * ユーザプロフィール画像をS3へアップロード
     *
     * @param object $img
     * @return string img_url
     */
    public function putUserImage(string $path)
    {
        dd(Image::make($path)->filesize());

        $path = Storage::disk('s3')->putFile('/img/user_profiles', $path, 'public');
        return Storage::disk('s3')->url($path);
    }

    /**
     * ファイル名がフロント側で生成したものかチェック
     *
     * @param string $filename
     * @return bool
     */
    public function isRegularFilename(string $filename)
    {
        return ($filename === $this->initial_image_name);
    }

    /**
     * @param object $image
     */
    public function encodeImage(object $param_img)
    {
        dump($param_img->getSize());
        $uuid = (string)Str::uuid();
        $path = "img/tmp/{$uuid}.jpg";         // 書き出した画像の一時パス

        $img = Image::make($param_img)
            ->encode('jpg');

        $quality = $this->getQuality($img->filesize());

        $img->save($path, $quality);
        //$this->saveImg($img, $path, $initialQuality);

        return $path;
    }

    private function getQuality(int $img_size_bite)
    {
        $initialQuality = 25720000 / $img_size_bite; //20000000

        dump($initialQuality);

        if($initialQuality >= 90){
            return 90;
        }else if($initialQuality <= 0){
            return 0;
        }

        return (int)$initialQuality;
    }

//    private function saveImg(object $img, string $path, int $quality)
//    {
//        dump($img->filesize());
//        $img->save($path, $quality);
//        $image = Image::make($path);
//
//        dump($image->filesize());
//        if($image->filesize() > $this->user_img_size_max_bite){
//            $quality -= 5;
//            dump($quality);
//
//            return $this->saveImg($img, $path, $quality);
//
//        }else if($image->filesize() < $this->user_img_size_min_bite){
//            $quality += 5;
//            dump($quality);
//
//            return $this->saveImg($img, $path, $quality);
//        }
//    }
}
