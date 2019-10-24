<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserInfoRequest extends FormRequest
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
            //
            'name' => 'required',
            'post' => 'required',
            'phone' => 'required',
            'email' => 'nullable|email'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '姓名不能为空',
            'post.required' => '职位不能为空',
            'phone.required' => '手机号不能为空',
            'email.email' => '邮箱格式错误'
        ];
    }
}
