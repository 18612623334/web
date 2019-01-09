<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class ModifyPhoneTwoRequest extends FormRequest
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
            'password' => ['required', 'between:6,15', 'regex:/^([A-Z]|[a-z]|[0-9]|[_.]){6,15}$/', 'confirmed'],
            'password_confirmation' => ['required', 'between:6,15', 'regex:/^([A-Z]|[a-z]|[0-9]|[_.]){6,15}$/']
        ];
    }


    public function message()
    {
        return [
            'password.required' => '请输入密码',
            'password.between' => '请输入6~15位密码',
            'password.regex' => '只允许数字、字母、下划线',
            'password_confirmation.required' => '请输入确认密码',
            'password_confirmation.between' => '请输入6~15位确认密码',
            'password_confirmation.regex' => '只允许数字、字母、下划线',
            'password.confirmed' => '两次密码输入不一致',
        ];
    }
}
