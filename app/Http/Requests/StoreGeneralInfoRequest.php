<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'receipt' => 'required',
            'jalaliMonth' => [
                'required',
                Rule::unique('general_infos')->where(function ($query) {
                    return $query->where('jalaliYear', $this->input('jalaliYear'));
                })
            ],
            'jalaliYear' => 'required',
        ];
    }
    
    // Messages
    public function messages()
    {
        return [
            'jalaliMonth.unique' => 'برای تاریخ انتخاب شده "گزارش کلی" موجود است',
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
            'receipt' => '"رسید صورتحساب بانک"',
        ];
    }
}
