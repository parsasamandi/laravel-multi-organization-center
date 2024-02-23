<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGeneralInfoRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'bank_balance' => 'required',
            'bank_statement_receipt' => 'required'
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
            'bank_balance' => '"موجودی در بانک"',
            'bank_statement_receipt' => '"پرینت صورتحساب بانکی"',
        ];
    }
}
