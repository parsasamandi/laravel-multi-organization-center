<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGeneralInfoRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Define the base validation rules
        return [
            'bank_balance' => 'required',
            'jalaliMonth' => 'required',
            'jalaliYear' => 'required',
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
        ];
    }
}
