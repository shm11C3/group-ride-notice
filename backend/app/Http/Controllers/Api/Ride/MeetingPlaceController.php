<?php

namespace App\Http\Controllers\Api\Ride;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateMeetingPlaceRequest;
use Illuminate\Support\Str;

class MeetingPlaceController extends Controller
{
    /**
     * 集合場所を作成
     * 
     * @param App\Http\Requests\CreateMeetingPlaceRequest
     * @return bool
     */
    public function createMeetingPlace(CreateMeetingPlaceRequest $request)
    {
        $meeting_place_uuid = Str::uuid();
        $user_uuid = Auth::user()->uuid;

        DB::beginTransaction();
        try{
            DB::table('meeting_places')
            ->insert([
                'uuid' => $meeting_place_uuid,
                'user_uuid' => $user_uuid,
                'name' => $request['name'],
                'address' => $request['address'],
                'publish_status' => $request['publish_status'],
            ]);

            if($request['save_status']){

                //保存するを選択した場合
                DB::table('saved_meeting_places')
                ->insert([
                    'meeting_place_uuid' => $meeting_place_uuid,
                    'user_uuid' => $user_uuid,
                    'meeting_place_category_id' => 0,
                ]);
            }

            $data = ['status' => true];

            DB::commit();
        }catch(\Throwable $e){
            DB::rollback();
            abort(500);
        }

        return response()->json($data);
    }
}
