<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use App\Providers\EnglishConvertion;

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
            'description' => 'required',
            'type' => 'required',
        ];

        if(!$this->has('id')) {
            $rules['receipt'] = 'required';
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
            'range' => $englishConvertion->convert($this->input('range')),
            'expenses' => $englishConvertion->convert($this->input('expenses'))
        ]);
    }
}
