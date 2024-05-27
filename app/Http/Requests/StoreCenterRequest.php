<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Providers\EnglishConvertion;
use Illuminate\Http\Request;

// تیم گلستان
// برای ماه و سال انتخاب شده، صورتحساب قبلا ثبت شده است
class StoreCenterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'name' => 'required',
            'password' => 'required|min:6|',
            'password-confirm' => 'same:password',
            'phone_number' => 'required|numeric|digits:11|unique:centers,phone_number,' . $request->get('id'),
            'email' => 'email|max:255|unique:centers,email,' . $request->get('id')
        ];
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
            'phone_number' => 'تلفن همراه'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // English convertion
        $englishConvertion = new EnglishConvertion();

        $this->merge([
            'phone_number' => $englishConvertion->convert($this->input('phone_number'))
        ]);
    }
}
