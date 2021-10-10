<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
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
        //dd(Password::min(8)->uncompromised(50));
        return [
            'current_password' => 'required|current_password',
            'new_password' =>  ['confirmed', 'max:64', Password::min(8)->uncompromised(5)],
            'new_password_confirmation' => 'required'
        ];
    }
}
