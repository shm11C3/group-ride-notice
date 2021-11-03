<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;

    public function isFollowed(string $user_by, string $user_to){
        $isFollowed = DB::table('follows')
            ->where('user_by', $user_by)
            ->where('user_to', $user_to)
            ->exists();

        return $isFollowed;
    }
}
