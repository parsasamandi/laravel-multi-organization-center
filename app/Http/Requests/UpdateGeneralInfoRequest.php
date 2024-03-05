<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class UpdateGeneralInfoRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // Check if "general_info" exists for the given month and year
        $jalaliMonth = $this->input('jalaliMonth');
        $jalaliYear = $this->input('jalaliYear');

        $generalInfoExists = GeneralInfo::where('jalaliMonth', $jalaliMonth)
                                      ->where('jalaliYear', $jalaliYear)
                                      ->where('center_id', auth()->id())
                                      ->exists();

        // Define validation rules
        $rules = [
            'expenses' => 'required',
            'range' => 'required',
            'description' => 'required',
            'type' => 'required',
            'jalaliMonth' => 'required', // Assuming this is also a required field
            'jalaliYear' => 'required', // Assuming this is also a required field
        ];

        // Add custom validation rule to check for "general_info" existence
        if (!$generalInfoExists) {
            $rules['general_info'] = 'required'; // Add any custom rule here
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
            'expenses' => 'هزینه',
            'range' => 'ردیف های بانکی',
            'jalaliMonth' => 'ماه',
            'jalaliYear' => 'سال',
            'general_info' => 'مقدمات گزارش'
        ];
    }

    /**
     * Return custom JSON response when validation fails.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     */
    protected function failedValidation($validator)
    {
        $response = new JsonResponse([
            'success' => false,
            'errors' => $validator->errors(),
            'message' => '<div class="alert alert-danger">مقدمات گزارش برای تاریخ مورد نظر وجود ندارد.</div>' 
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
