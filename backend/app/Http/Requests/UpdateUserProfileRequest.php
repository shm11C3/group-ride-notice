<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfileRequest extends FormRequest
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
            'user_intro' => 'string|max:128',
            'user_url' => 'url|max128',
            'fb_username' => 'string|max: 64',
            'tw_username' => 'string|max: 14',
            'ig_username' => 'string|max: 30',
            'name' => 'required|string|max:32',
            'prefecture_code' => 'required|numeric|between:1,47'
        ];
    }
}
