<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use App\Models\GeneralInfo;

class UpdateReportRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        return [
            'expenses' => 'required',
            'range' => 'required',
            'description' => 'required',
            'type' => 'required',
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
            'expenses' => 'هزینه',
            'range' => 'ردیف های بانکی',
            'jalaliMonth' => 'ماه',
            'jalaliYear' => 'سال',
        ];
    }

     /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        // Add a custom validation rule to check if "general_info" exists for the given month and year
        $validator->after(function ($validator) {
            $jalaliMonth = $this->input('jalaliMonth');
            $jalaliYear = $this->input('jalaliYear');

            $generalInfo = GeneralInfo::where('jalaliMonth', $jalaliMonth)
                                      ->where('jalaliYear', $jalaliYear)
                                      ->where('center_id', auth()->id())
                                      ->exists();

            if (!$generalInfo) {
                $validator->errors()->add('general_info', 'مقدمات گزارش برای تاریخ مورد نظر وجود ندارد.');
            }
        });
    }


    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'success' => false,
            'message' => '<div class="alert alert-danger">در گذشته برای تاریخ انتخاب شده "مقدمات گزارش"وارد نشده است</div>'
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        throw new HttpResponseException($response);
    }



}
