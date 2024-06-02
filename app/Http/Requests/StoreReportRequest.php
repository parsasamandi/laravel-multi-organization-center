<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Providers\Convertor;
use App\Http\Requests\Rules\GeneralInfoExists;
use App\Http\Requests\Rules\CommaSeparatedNumbers;

class StoreReportRequest extends FormRequest
{
    public function rules()
    {
        $rules = [
            'expenses' => 'required|numeric',
            'range' => [
                'required',
                'numeric', // Ensures all characters are numbers
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
            'expenses' => 'هزینه مبلغ',
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
        ];
    }
}
