<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRideRouteRequest extends FormRequest
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
            'name' => 'required|string|min:1|max:32',
            'elevation' => 'numeric|min:0|max:65535',
            'distance' => 'required|numeric|min:0|max:65535',
            'num_of_laps' => 'numeric|min:0|max:255',
            'comment' => 'required|string|min:0|max:512',
            'publish_status' => 'required|numeric|min:0|max:2',
            'save_status' => 'required|boolean'
        ];
    }
}
