<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeacherTimesRequest extends FormRequest
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
            'date_at' => 'date_format:Ymd|required',
            'arr.*.start_at' => 'date_format:Ymd H:i|required',
            'arr.*.end_at' => 'date_format:Ymd H:i|required',
        ];
    }
}
