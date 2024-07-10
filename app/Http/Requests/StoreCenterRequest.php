<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Providers\Convertor;

class StoreCenterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $id = $request->get('id') ? Crypt::decryptString($request->get('id')) : 'NULL'; // Use 'NULL' if id is not set

        $rules = [  
            'name' => 'required',
            'code' => $request->get('id') 
                ? 'nullable|numeric|digits:2|unique:centers,code,' . $id 
                : 'required|numeric|digits:2|unique:centers,code,' . $id,
            'password' => $request->get('id') 
                ? 'nullable|min:7|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/' 
                : 'required|min:7|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/',
            'password-confirm' => 'same:password',
            'phone_number' => [
                'required',
                'numeric',
                'regex:/^\d{9}$|^\d{11}$/',
                'unique:centers,phone_number,' . $id
            ],
            'email' => 'email|max:255|unique:centers,email,' . $id
        ];

        // Apply the validation rules
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
            'password-confirm' => "تاییدیه رمز عبور",
            'phone_number' => 'تلفن همراه',
            'code' => 'کد مرکز'
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
            'phone_number' => $convertor->persianToEnglishDecimal($this->input('phone_number')),
            'code' => $convertor->persianToEnglishDecimal($this->input('code')),
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
            'code.digits' => 'کد مرکز باید دو رقم باشد.',
            'phone_number.regex' => 'تلفن همراه باید نه یا یازده رقم باشد.',
            'password.min' => 'رمز عبور نباید کمتر از هفت حرف باشد.',
            'password.regex' => 'رمز عبور باید حداقل یک حرف کوچک، یک حرف بزرگ و یک عدد داشته باشد.'
        ];
    }
}
