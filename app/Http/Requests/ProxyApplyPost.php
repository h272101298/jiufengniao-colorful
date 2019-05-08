<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ProxyApplyPost extends FormRequest
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
            'phone'=>'required',
            'name'=>'required',
            'sex'=>'required|numeric',
            'bank'=>'required',
            'account'=>'required|numeric'
        ];
    }
    public function failedValidation(Validator $validator)
    {
        return jsonResponse([
            'msg'=>$validator->errors()->first()
        ],422);
    }
}
