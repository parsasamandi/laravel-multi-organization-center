<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Providers\Convertor;
use Auth;

class StoreGeneralInfoRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'bank_balance' => 'required|numeric',
            'jalaliYear' => 'required',
        ];

        // If 'id' is not present in the request, make 'receipt' required
        if (!$this->input('id')) {
            $rules['receipt'] = 'required';
        }

        // Unique jalaliMonth validation based on Jalali year and center_id
        $rules['jalaliMonth'] = [
            'required',
            Rule::unique('general_infos')
                ->where(function ($query) {
                    return $query->where('jalaliYear', $this->input('jalaliYear'))
                        ->where('center_id', Auth::id());
                })
                ->ignore($this->input('id'), 'id') // Ignore current record ID during update
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
            'receipt' => 'رسید',
            'bank_balance' => 'مانده بانک',
            'jalaliYear' => 'سال',
            'jalaliMonth' => 'ماه',
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
            'bank_balance' => $convertor->persianToEnglishDecimal($this->input('bank_balance'))
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
            'receipt.required' => 'پیوست فایل صورتحساب بانکی الزامی است.',
            'jalaliMonth.unique' => 'این ماه برای سال انتخاب شده قبلا وارد شده است.',
        ];
    }
}
