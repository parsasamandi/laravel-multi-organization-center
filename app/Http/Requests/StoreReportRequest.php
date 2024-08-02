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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $centerId = $this->getCenterId();

        return [
            'expenses' => 'required|numeric',
            'range' => 'required|regex:/.*\d+.*$/',
            'receipt' => $this->input('id') 
                ? 'nullable|mimes:xls,xlsx,pdf,doc,docx,csv|max:15000' 
                : 'required|mimes:xls,xlsx,pdf,doc,docx,csv|max:15000',
            'jalaliMonth' => ['required', new GeneralInfoExists($this->get('jalaliYear'), $this->get('jalaliMonth'), $centerId)],
            'jalaliYear' => 'required',
            'type' => 'required'
        ];
    }

    // Get Center Id
    protected function getCenterId()
    {
        if ($this->input('id')) {
            try {
                $decryptedId = Crypt::decryptString($this->input('id'));
                $report = Report::find($decryptedId);
                return $report ? $report->center_id : null;
            } catch (\Exception $e) {
                return null;
            }
        }

        return Auth::user()->id;
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
