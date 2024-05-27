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
            'expenses' => 'required | numeric',
            'range' => 'required | numeric',
            'description' => 'required',
            'type' => 'required'
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
            'range' => 'ردیف های هزینه در صورتحساب',
            'jalaliMonth' => 'ماه',
            'jalaliYear' => 'سال',
        ];
    }

}
