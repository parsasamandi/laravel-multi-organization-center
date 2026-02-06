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
    protected $decryptedId = null;
    protected $centerId = null;

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // English conversion
        $convertor = new Convertor();
        $this->merge([
            'bank_balance' => $convertor->persianToEnglishDecimal($this->input('bank_balance'))
        ]);

        // Decrypt the ID if it's present
        if ($this->input('id')) {
            try {
                $this->decryptedId = Crypt::decryptString($this->input('id'));
                $this->centerId = GeneralInfo::find($this->decryptedId)->center_id;
            } catch (\Exception $e) {
                $this->decryptedId = null;
                $this->centerId = Auth::user()->id;
            }
        } else {
            // Get the authenticated user's center ID
            $this->centerId = Auth::user()->id;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'bank_balance' => 'required|numeric',
            'jalaliYear' => 'required',
            'receipt' => $this->input('id') ? 'nullable|mimes:xls,xlsx,pdf,doc,docx,csv|max:24000' 
            : 'required|mimes:xls,xlsx,pdf,doc,docx,csv|max:24000',
            'jalaliMonth' => [
                'required',
                Rule::unique('general_infos')
                    ->where(function ($query) {
                        return $query->where('jalaliYear', $this->input('jalaliYear'))
                                     ->where('center_id', $this->centerId)
                                     ->whereExists(function ($query) {
                                         $query->select(DB::raw(1))
                                               ->from('centers')
                                               ->whereColumn('centers.id', 'general_infos.center_id')
                                               ->where('centers.type', 0)
                                               ->where('centers.id', $this->centerId);
                                     });
                    })
                    ->ignore($this->decryptedId, 'id') // Ignore current record ID during update
            ]
        ];
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