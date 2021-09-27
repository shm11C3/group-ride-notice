<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Ride extends Model
{
    use HasFactory;

    public $tableArr = [
        '',
        ''
    ];



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
}