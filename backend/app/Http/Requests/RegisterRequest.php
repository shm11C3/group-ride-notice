<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'password'=>'required|regex:/^[0-9a-zA-Z]+$/|min:6|max:32',
        ];
    }
}
