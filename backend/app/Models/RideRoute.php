<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RideRoute extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $allowHostList = [
        'cloudfront.net'
    ];
}
