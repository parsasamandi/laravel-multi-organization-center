<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;
use App\Providers\Convertor;
use App\Models\GeneralInfo;
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
        // Decrypt the ID if it's present, or set to null if decryption fails
        $decryptedId = null;
        if ($this->input('id')) {
            $decryptedId = Crypt::decryptString($this->input('id'));

            $generalInfo = GeneralInfo::find($decryptedId);
            $centerId = $generalInfo->center_id;
        } else {
            // Get the authenticated user's center ID
            $centerId = Auth::user()->id;
            $decryptedId = null;
        }

        $rules = [
            'bank_balance' => 'required|numeric',
            'jalaliYear' => 'required',
            'receipt' => $this->input('id') ? 'nullable|mimes:xls,xlsx,pdf,doc,docx,csv|max:5096' : 'required|mimes:xls,xlsx,pdf,doc,docx,csv|max:5096',
        ];

        $rules['jalaliMonth'] = [
            'required',
            Rule::unique('general_infos')
                ->where(function ($query) use ($centerId) {
                    return $query->where('jalaliYear', $this->input('jalaliYear'))
                                 ->where('center_id', $centerId)
                                 ->whereExists(function ($query) use ($centerId) {
                                     $query->select(DB::raw(1))
                                           ->from('centers')
                                           ->whereColumn('centers.id', 'general_infos.center_id')
                                           ->where('centers.type', 0);
                                 });
                })
                ->ignore($decryptedId, 'id') // Ignore current record ID during update
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
