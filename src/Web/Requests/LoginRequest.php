<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'phone' => ['required', 'regex:/^1[123456789]\d{9}$/'],
            'password' => ['required', 'between:6,15', 'regex:/^([A-Z]|[a-z]|[0-9]|[_.]){6,15}$/']
        ];
    }


    public function message()
    {
        return [
            'phone.required' => '请输入手机号码',
            'phone.regex' => '手机号码格式错误',
            'password.required' => '请输入密码',
            'password.between' => '请输入6~15位密码',
            'password.regex' => '只允许数字、字母、下划线',
        ];
    }
}
