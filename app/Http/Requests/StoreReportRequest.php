<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Providers\EnglishConvertion;
use Illuminate\Http\Request;

class StoreReportRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $rules = [
            'expenses' => 'required',
            'range' => 'required',
            'receipt' => 'required',
            'description' => 'required',
            'type' => 'required',
        ];

        if(!$this->has('id')) {
            $rules['id'] = 'required';
        }
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'receipt' => '"رسید"',
            'expenses' => '"هزینه"',
            'range' => '"ردیف های هزینه در صورتحساب"',                       
        ];
    }
}
