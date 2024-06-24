<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use App\Providers\Convertor;

class StoreLoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $rules = [
            'phone_number' => 'required|numeric',
            'password' => 'nullable|min:7|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/',
        ];

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'password' => "رمز عبور",
            'phone_number' => 'تلفن همراه',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    public function prepareForValidation()
    {
        // English conversion
        $convertor = new Convertor();

        $this->merge([
            'phone_number' => $convertor->persianToEnglishDecimal($this->input('phone_number'))
        ]);
    }

     /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'phone_number.digits' => 'تلفن همراه باید یازده رقم باشد.',
            'password.min' => 'رمز عبور نباید کمتر از هفت حرف باشد.',
            'password.regex' => 'رمز عبور باید حداقل یک حرف کوچک، یک حرف بزرگ و یک عدد داشته باشد.'
        ];
    }
}
