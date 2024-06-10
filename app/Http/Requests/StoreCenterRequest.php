<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
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
        $rules = [
            'name' => 'required',
            'code' => 'required|numeric|digits:2',
            'phone_number' => 'required|numeric|digits:11|unique:centers,phone_number,' . $request->get('id'),
            'email' => 'email|max:255|unique:centers,email,' . $request->get('id')
        ];

        if(!$this->get('id')) {
            $rules['type'] = 'required';
            $rules['password'] = 'required|min:6|';
            $rules['password-confirm'] = 'same:password';
        }

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
        // English convertion
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
        ];
    }
}
