<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMeetingPlaceRequest extends FormRequest
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
            'prefecture_code' => 'required|numeric|between:1,47',
            'address' => 'required|string|min:1|max:255',
            'publish_status' => 'required|numeric|min:0|max:2',
            'save_status' => 'required|boolean'
        ];
    }
}
