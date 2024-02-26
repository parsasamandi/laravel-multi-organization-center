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
        // Define the base validation rules
        $rules = [
            'bank_balance' => 'required',
        ];

        // Check if the hidden_receipt field is not 'Not null'
        if ($this->input('hidden_receipt') != 'Not null') {
            // If true, add the validation rule for the receipt field
            $rules['receipt'] = 'required';
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
            'bank_balance' => '"موجودی در بانک"'
        ];
    }
}
