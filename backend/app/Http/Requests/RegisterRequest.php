<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'name'=>'required|string|max:32|',
            'email'=>'required|unique:users,email|email|max:255',
            'prefecture_code' => 'required|numeric|between:1,47',
            'password'=> ['required', 'max:64',Password::min(8)->uncompromised(5)],
            'remember'=>'boolean',
        ];
    }
}
