<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RideParticipant extends Model
{
    use HasFactory;

    public function ride()
    {
        return $this->belongsTo(Ride::class, 'uuid', 'ride_uuid');
    }

    /**
     * ride_participantsの重複チェック
     * 
     * @param string uuid
     * @return bool
     */
    public function ptIsRegistered(string $user_uuid, string $ride_uuid)
    {
        $isExist = RideParticipant::where('user_uuid', $user_uuid)
            ->where('ride_uuid', $ride_uuid)
            ->exists();
            
        if($isExist){
            return true;
        }
        return false;
    }
}
