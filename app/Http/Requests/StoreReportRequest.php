<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Crypt;
use App\Providers\Convertor;
use App\Models\GeneralInfo;
use App\Models\Report;
use Auth;

class StoreReportRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
    */
    public function rules()
    {
        return [
            'expenses' => 'required|numeric',
            'range' => 'required|regex:/.*\d+.*$/',
            'receipt' => $this->input('id') 
                ? 'nullable|mimes:xls,xlsx,pdf,doc,docx,csv|max:15000' 
                : 'required|mimes:xls,xlsx,pdf,doc,docx,csv|max:15000',
            'jalaliMonth' => ['required', function ($attribute, $value, $fail) {
                $this->checkGeneralInfoExists($fail);
            }],
            'jalaliYear' => 'required',
            'type' => 'required'
        ];
    }


    /**
     * Check if generalInfo exists.
     *
     * @return error
     */
    protected function checkGeneralInfoExists($fail)
    {
        // Jalali year select box
        $jalaliYear = $this->input('jalaliYear');
        // Jalali month select box
        $jalaliMonth = $this->input('jalaliMonth');

        // Center Id
        $encryptedId = $this->input('id');
        $centerId = $encryptedId
            ? Report::where('id', Crypt::decryptString($encryptedId))->value('center_id')
            : null;

        // Check if it exists
        $exists = GeneralInfo::where('center_id', $centerId)
            ->where('jalaliMonth', $jalaliMonth)
            ->where('jalaliYear', $jalaliYear)
            ->exists();

        if (!$exists) {
            $fail('برای سال و ماه انتخاب شده، قبلا گزارش صورتحساب وارد نشده است. لطفا ابتدا گزارش صورتحساب را برای این تاریخ وارد نمایید.');
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
            'receipt' => 'رسید',
            'expenses' => 'مبلغ هزینه',
            'range' => 'ردیف های هزینه در صورتحساب',
        ];
    }


    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
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
            'range.regex' => 'لطفا در ردیف های هزینه صورتحساب بانکی، رشته‌ای از اعداد وارد کنید. (لطفا با ویرگول جدا شود)',
        ];
    }
}
