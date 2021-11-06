<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ParticipationRequest;
use App\Http\Requests\CancelParticipationRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\RideParticipant;

class ParticipationController extends Controller
{

    public function __construct(RideParticipant $participation)
    {
        $this->participation = $participation;
    }

    /**
     * ライドの参加登録
     *
     * @param App\Http\Requests\ParticipationRequest;
     * @return response
     */
    public function participationRegister(ParticipationRequest $request)
    {
        $user_uuid = Auth::user()->uuid;
        $uuid = Str::uuid();
        $ride_uuid = $request['ride_uuid'];


        if($this->participation->ptIsRegistered($user_uuid, $ride_uuid)){
            return response()->json(['status' => 0]);

        }else{
            DB::table('ride_participants')
            ->insert([
                'uuid' => $uuid,
                'user_uuid' => $user_uuid,
                'ride_uuid' => $ride_uuid,
                'comment' => $request['comment']
            ]);

            return response()->json(['status' => 1, 'uuid' => $uuid]);
        }
    }

    /**
     * ライドの参加解除
     *
     * @param App\Http\Requests\CancelParticipationRequest;
     * @return response
     */
    public function cancelParticipation(CancelParticipationRequest $request)
    {
        $user_uuid = Auth::user()->uuid;
        $uuid = Str::uuid();
        $ride_uuid = $request['ride_uuid'];


        if(!$this->participation->ptIsRegistered($user_uuid, $ride_uuid) || $this->participation->isLoginUser($user_uuid, $ride_uuid) ){
            return response()->json(['status' => 0]);

        }else{
            //登録済みの場合は解除
            DB::table('ride_participants')
            ->where('user_uuid', $user_uuid)
            ->where('ride_uuid', $ride_uuid)
            ->delete();

            return response()->json(['status' => -1, 'uuid' => $uuid]);
        }
    }
}
