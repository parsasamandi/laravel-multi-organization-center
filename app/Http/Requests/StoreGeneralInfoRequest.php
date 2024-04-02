<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Providers\EnglishConvertion;
use App\Models\GeneralInfo;
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
        // Validation rules
        return [
            'bank_balance' => 'required | numeric',
            'receipt' => 'required',
            'jalaliMonth' => [
                'required',
                Rule::unique('general_infos')->where(function ($query) {
                    return $query->where('jalaliYear', $this->input('jalaliYear'))
                        ->where('center_id', Auth::id());
                })
            ],
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
            'receipt' => '"رسید صورت حساب بانکی"',
            'bank_balance' => '"موجودی در پایان ماه"',
            'jalaliMonth' => 'ماه و سال',                  
        ];
    }
}
