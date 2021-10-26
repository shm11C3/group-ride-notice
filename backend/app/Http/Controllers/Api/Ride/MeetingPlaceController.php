<?php

namespace App\Http\Controllers\Api\Ride;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateMeetingPlaceRequest;
use App\Http\Requests\RegisterMeetingPlaceRequest;
use Illuminate\Support\Str;
use App\Models\Ride;

class MeetingPlaceController extends Controller
{
    public function __construct(Ride $ride)
    {
        $this->ride = $ride;
    }

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
                'prefecture_code' => $request['prefecture_code'],
                'address' => $request['address'],
                'publish_status' => $request['publish_status'],
            ]);

            if($request['save_status']){
                //保存するを選択した場合
                $this->saveMeetingPlace($user_uuid, $meeting_place_uuid);
            }

            $data = [
                'status' => true,
                'uuid' => $meeting_place_uuid,
            ];

            DB::commit();
        }catch(\Throwable $e){
            DB::rollback();
            abort(500);
        }

        return response()->json($data);
    }

    /**
     * 集合場所登録
     *
     * @param string RegisterMeetingPlaceRequest
     * @return response
     */
    public function registerMeetingPlace(RegisterMeetingPlaceRequest $request)
    {
        $register = $this->saveMeetingPlace(Auth::user()->uuid, $request['meeting_place_uuid']);

        $result = ['status' => $register];
        return response()->json($result);
    }

    /**
     * 集合場所登録テーブルに挿入
     *
     * @param string $meeting_place_uuid
     * @param string $user_uuid
     * @return bool
     */
    public function saveMeetingPlace(string $user_uuid, string $meeting_place_uuid)
    {
        DB::table('saved_meeting_places')
        ->insert([
            'meeting_place_uuid' => $meeting_place_uuid,
            'user_uuid' => $user_uuid,
            'meeting_place_category_id' => 0,
        ]);

        return true;
    }

    /**
     * 保存した集合場所を取得
     *
     * @param void
     * @return object $data
     */
    public function getSavedMeetingPlaces()
    {
        $user_uuid = Auth::user()->uuid;

        $dbData = DB::table('saved_meeting_places')
        ->where('saved_meeting_places.user_uuid', $user_uuid)
        ->join('meeting_places','meeting_place_uuid','meeting_places.uuid')
        ->get([
            'meeting_places.uuid',
            'meeting_places.user_uuid',
            'name',
            'prefecture_code',
            'address',
        ]);

        $data = ['data'=>$dbData, 'key'=>'saved_meeting_places'];

        return response()->json($data);
    }

    /**
     * 公開されているすべての集合場所を取得
     *
     * @param int $prefecture_code
     * @return response
     */
    public function getAllMeetingPlaces($prefecture_code)
    {
        $user = Auth::user();
        $user_uuid = $user->uuid ?? 0;

        $operator = $this->ride->getOperatorByPrefectureCode($prefecture_code);

        $meeting_places_dbData = DB::table('meeting_places')
            ->where('prefecture_code', $operator, $prefecture_code)
            ->where('publish_status', 0)
            ->orWhere('meeting_places.user_uuid', $user_uuid)
            ->where('prefecture_code', $operator, $prefecture_code)
            ->orderBy('id' ,'desc')
            ->select(
                '*'
            )
            ->simplePaginate(60);

        // (array) 保存済みの集合場所
        $registeredMeetingPlaces = $this->ride->isRegisteredMeetingPlace($meeting_places_dbData, $user_uuid);

        // すでにsaved_meeting_placesに登録済みかを判定し、オブジェクトを作成
        foreach($meeting_places_dbData as $i => $meeting_place_dbData){

            $meeting_place_uuid = $meeting_place_dbData->uuid;

            if(array_search($meeting_place_uuid, $registeredMeetingPlaces) !== false){
                $registered = true; // 登録済みの場合

            }else{
                $registered = false; // 登録済みの場合
            }

            //結果からオブジェクトを作成
            $meeting_places[$i] = (object) [
                'data' => $meeting_place_dbData,
                'isRegistered' => $registered
            ];
        }

        $data = [
            'auth_uuid' => $user_uuid,
            'meeting_places' => $meeting_places
        ];

        return response()->json($data);
    }
}
