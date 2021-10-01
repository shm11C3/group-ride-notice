<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRideRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $limitMonth = date("Y-m-d H:i", strtotime("12 month"));
        return [
            'meeting_places_uuid' => 'required|uuid|exists:meeting_places,uuid',
            'ride_routes_uuid' => 'required|uuid|exists:ride_routes,uuid',
            'name' => 'required|string|min:1|max:32',
            'time_appoint' => 'required|date_format:Y-m-d H:i|after:today|before:'.$limitMonth,
            'intensity' => 'required|min:0|max:10',
            'num_of_laps' => 'numeric|min:0|max:255',
            'comment' => 'required|string|min:1|max:1024',
            'publish_status' => 'required|min:0|max:3'
        ];
    }
}
