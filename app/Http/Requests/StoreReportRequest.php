<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Providers\Convertor;
use App\Http\Requests\Rules\GeneralInfoExists;
use App\Http\Requests\Rules\CommaSeparatedNumbers;
use Auth;

class StoreReportRequest extends FormRequest
{
    public function rules()
    {
        $rules = [
            'expenses' => 'required|numeric',
            'range' => [
                'required',
            ],
            'jalaliMonth' => ['required', new GeneralInfoExists($this->get('jalaliYear'), $this->get('jalaliMonth'))],
            'jalaliYear' => ['required'],
            'type' => [
                'required',
                Rule::unique('reports')
                    ->where(function ($query) {
                        $jalaliYear = $this->input('jalaliYear');
                        $jalaliMonth = $this->input('jalaliMonth');

                        $query->where('general_info_id', function ($subQuery) use ($jalaliYear, $jalaliMonth) {
                            $subQuery->select('id')
                                    ->from('general_infos')
                                    ->where('center_id', Auth::user()->id)
                                    ->where('jalaliYear', $jalaliYear)
                                    ->where('jalaliMonth', $jalaliMonth);
                        })
                        ->where('type', $this->input('type'));
                    })
                    ->ignore($this->input('id'), 'id')  // Ignore current record ID during update
            ],
        ];

        if (!$this->input('id')) {
            $rules['receipt'] = 'required';
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'receipt' => 'رسید',
            'expenses' => 'مبلغ هزینه',
            'range' => 'ردیف های هزینه در صورتحساب',   
        ];
    }

    protected function prepareForValidation()
    {
        $convertor = new Convertor();

        $this->merge([
            'range' => $convertor->persianToEnglishDecimal($this->input('range')),
            'expenses' => $convertor->persianToEnglishDecimal($this->input('expenses'))
        ]);
    }

    public function messages()
    {
        return [
            'receipt.required' => 'پیوست فایل رسید الزامی است.',
            'type.unique' => 'نوع هزینه قبلا برای سال و ماه انتخاب شده وارد شده است.',
            'range.regex' => 'نوع هزینه قبلا برای سال و ماه انتخاب شده وارد شده است.',
        ];
    }
}
