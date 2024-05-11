<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\GeneralInfo;

class UpdateGeneralInfoRequest extends FormRequest
{
   
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Validation rules
        return [
            'bank_balance' => 'double',
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
            'receipt' => '"رسید صورت‌حساب بانکی"',
            'bank_balance' => '"موجودی در پایان ماه"',                 
        ];
    }
}
