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
            'jalaliMonth' => [
                'required',
                Rule::unique('general_infos')->where(function ($query) {
                    return $query->where('jalaliYear', $this->input('jalaliYear'));
                })
            ],
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

}
