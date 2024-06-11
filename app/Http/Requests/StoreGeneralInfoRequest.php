<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Providers\Convertor;
use DB;
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
            'receipt' => 'nullable|mimes:xls,xlsx,pdf,doc,docx,csv|max:5096',
        ];

        // If 'id' is not present in the request, make 'receipt' required
        if (!$this->input('id')) {
            $data['receipt'] = 'required|mimes:xls,xlsx,pdf,doc,docx,csv|max:5096';
        }

        $rules['jalaliMonth'] = [
            'required',
            Rule::unique('general_infos')
                ->where(function ($query) {
                    $userId = Auth::id();
                    return $query->where('jalaliYear', $this->input('jalaliYear'))
                                 ->where('center_id', $userId)
                                 ->whereExists(function ($query) use ($userId) {
                                     $query->select(DB::raw(1))
                                           ->from('centers')
                                           ->whereColumn('centers.id', 'general_infos.center_id')
                                           ->where('centers.type', 0)
                                           ->where('centers.id', $userId);
                                 });
                })
                ->ignore($this->input('id'), 'id') // Ignore current record ID during update
        ];


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
            'bank_balance' => 'موجودی پایان ماه',
            'jalaliYear' => 'سال',
            'jalaliMonth' => 'ماه',
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
            'bank_balance' => $convertor->persianToEnglishDecimal($this->input('bank_balance'))
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
            'receipt.required' => 'پیوست فایل صورتحساب بانکی الزامی است.',
            'jalaliMonth.unique' => 'این ماه برای سال انتخاب شده قبلا وارد شده است.',
        ];
    }
}
