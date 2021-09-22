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
        return [
            'meeting_places_uuid' => 'required|uuid|meeting_places,uuid',
            'ride_routes_uuid' => 'required|uuid|exists:ride_routes,uuid',
            'name' => 'required|string|min:1|max:32',
            'time_appoint' => 'required|date_format:Y-m-d H:i:s',
            'intensity' => 'required|min:0|max:10',
            'comment' => 'required|string|min:1|max:1024',
            'publish_status' => 'required|min:0|max:3'
        ];
    }
}