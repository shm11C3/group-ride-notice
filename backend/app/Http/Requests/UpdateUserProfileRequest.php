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
            'user_intro' => 'nullable|string|max:128',
            'user_url' => 'nullable|url|max: 128|active_url',
            'fb_username' => 'nullable|string|max: 64|regex: /^[a-z\d.]{5,}$/i',
            'tw_username' => 'nullable|alpha_dash|max: 14',
            'ig_username' => 'nullable|string|max: 30',
            'name' => 'required|string|max:32',
            'prefecture_code' => 'required|numeric|between:1,47'
        ];
    }
}
