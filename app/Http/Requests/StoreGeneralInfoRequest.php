<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
            $rules['jalaliMonth'] = [
                'required',
                Rule::unique('general_infos')->where(function ($query) {
                    return $query->where('jalaliYear', $this->input('jalaliYear'))
                        ->where('center_id', Auth::id());
                })
            ];
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
            'receipt' => '"رسید صورتحساب بانکی"',
            'bank_balance' => '"موجودی در پایان ماه"',
            'jalaliMonth' => 'ماه و سال',
        ];
    }
}
