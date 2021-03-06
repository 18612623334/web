<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class SmsRequest extends FormRequest
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
        ];
    }


    public function message()
    {
        return [
            'phone.required' => '请输入手机号码',
            'phone.regex' => '手机号码格式错误',
        ];
    }
}
