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
        $id = $request->get('id') ? Crypt::decryptString($request->get('id')) : 'NULL'; // Use 'NULL' if id is not set

        $rules = [
            'name' => 'required',
            'code' => 'numeric|unique:centers,code,' . $id,
            'password' => 'nullable|min:7|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/',
            'phone_number' => 'required|numeric|digits:11|unique:centers,phone_number,' . $id,
            'email' => 'email|max:255|unique:centers,email,' . $id
        ];

        if (!$this->get('id')) {
            $rules['code'] = 'required|numeric|digits:2|unique:centers';
            $rules['password'] = 'required|min:7|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/';
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
            'phone_number.digits' => 'تلفن همراه باید یازده رقم باشد.',
            'password.regex' => 'رمز عبور باید حداقل یک حرف کوچک، یک حرف بزرگ و یک عدد داشته باشد.'
        ];
    }
}
