<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;
use App\Providers\Convertor;
use App\Http\Requests\Rules\GeneralInfoExists;
use App\Http\Requests\Rules\CommaSeparatedNumbers;
use App\Models\Center;
use App\Models\Report;
use Auth;

class StoreReportRequest extends FormRequest
{
    public function rules()
    {
        // Decrypt the ID if it's present, or set to null if decryption fails
        $decryptedId = null;
        if ($this->input('id')) {
            $decryptedId = Crypt::decryptString($this->input('id'));

            $report = Report::find($decryptedId);
            $centerId = $report->center_id;
        } else {
            // Get the authenticated user's center ID
            $centerId = Auth::user()->id;
        }

        $rules = [
            'expenses' => 'required|numeric',
            'range' => 'required|regex:/.*\d+.*$/',
            'receipt' => $this->input('id') ? 'nullable|mimes:xls,xlsx,pdf,doc,docx,csv|max:5096' : 'required|mimes:xls,xlsx,pdf,doc,docx,csv|max:5096',
            'jalaliMonth' => ['required', new GeneralInfoExists($this->get('jalaliYear'), $this->get('jalaliMonth'), $centerId)],
            'jalaliYear' => ['required'],
            'type' => [
                'required',
                Rule::unique('reports')
                    ->where(function ($query) use ($centerId) {
                        $jalaliYear = $this->input('jalaliYear');
                        $jalaliMonth = $this->input('jalaliMonth');

                        $query->where('general_info_id', function ($subQuery) use ($jalaliYear, $jalaliMonth, $centerId) {
                            $subQuery->select('id')
                                    ->from('general_infos')
                                    ->where('center_id', $centerId)
                                    ->where('jalaliYear', $jalaliYear)
                                    ->where('jalaliMonth', $jalaliMonth);
                        })
                        ->where('type', $this->input('type'));
                    })
                    ->ignore($decryptedId, 'id')  // Ignore current record ID during update
            ],
        ];

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
            'range.regex' => 'لطفا در ردیف های هزینه صورتحساب بانکی، رشته‌ای از اعداد وارد کنید.',
        ];
    }
}
