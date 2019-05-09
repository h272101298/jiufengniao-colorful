<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UserAdressPost extends FormRequest
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
            'city'=>'required',
            'address'=>'required',
            'name'=>'required',
            'phone'=>'required'
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new \HttpResponseException(response()->json(['msg'=>$validator->errors()->first()],
            422));
    }
}
