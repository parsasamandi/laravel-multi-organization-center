<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Providers\Convertor;

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
            'expenses' => 'required|numeric',
            'range' => 'required',
            'type' => [
                'required',
                Rule::unique('reports')
                    ->where(function ($query) use ($request) {
                        $jalaliYear = $request->get('jalaliYear');
                        $jalaliMonth = $request->get('jalaliMonth');

                        $query->where('general_info_id', function ($subQuery) use ($jalaliYear, $jalaliMonth) {
                            $subQuery->select('id')
                                ->from('general_infos')
                                ->where('jalaliYear', $jalaliYear)
                                ->where('jalaliMonth', $jalaliMonth);
                        })
                        ->where('type', $request->get('type'));
                    })
                    ->ignore($request->get('id'), 'id')  // Ignore current record ID during update
            ],
        ];

        if (!$request->get('id')) {
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
            'receipt' => 'رسید',
            'expenses' => 'هزینه',
            'range' => 'ردیف های هزینه در صورتحساب',                       
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
            'range' => $convertor->persianToEnglishDecimal($this->input('range')),
            'expenses' => $convertor->persianToEnglishDecimal($this->input('expenses'))
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
            'receipt.required' => 'پیوست فایل رسید الزامی است.',
            'type.unique' => 'نوع هزینه قبلا برای سال و ماه انتخاب شده وارد شده است.',
        ];
    }
}

