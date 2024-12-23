<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Providers\Convertor;

class StorePaymentTransferRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'center_id' => ['required', 'exists:centers,id'], 
            'date' => ['nullable', 'date_format:Y-m-d'],
            'cad_to_usd_rate' => ['required', 'numeric', 'min:0'], 
            'total_rial' => ['required', 'numeric', 'min:0'], 
            'total_cad' => ['required', 'numeric', 'min:0', function($attribute, $value, $fail) {
                // Ensure total_cad is a number (either float or integer)
                $totalCad = (float) $this->input('total_cad', 0); // Convert to float, default to 0

                // Sum the values of the relevant fields, ensuring each is treated as a float
                $sum = 
                    (float) $this->input('operation', 0) + 
                    (float) $this->input('outfit', 0) + 
                    (float) $this->input('education', 0) + 
                    (float) $this->input('salary', 0) + 
                    (float) $this->input('food', 0) + 
                    (float) $this->input('misc', 0);

                // Validate the sum against total_cad
                if ($sum > $totalCad || $sum < $totalCad) {
                    $fail('The sum of payment must be equal with the total CAD amount.');
                }
            }],
            'operation' => ['nullable', 'numeric', 'min:0'], 
            'outfit' => ['nullable', 'numeric', 'min:0'], 
            'education' => ['nullable', 'numeric', 'min:0'], 
            'salary' => ['nullable', 'numeric', 'min:0'], 
            'food' => ['nullable', 'numeric', 'min:0'], 
            'misc' => ['nullable', 'numeric', 'min:0'], 
            'misc_desc' => ['nullable', 'string', 'max:300'],
        ];
    }

    public function messages()
    {
        return [
            'center_id.required' => 'The center name is required.',
            'center_id.exists' => 'The selected center ID is invalid.',
            'date.date_format' => 'The date must be in the format Y-m-d.',
            'cad_to_usd_rate.required' => 'The CAD to USD rate is required.',
            'cad_to_usd_rate.numeric' => 'The CAD to USD rate must be a number.',
            'cad_to_usd_rate.min' => 'The CAD to USD rate must be at least 0.',
            'total_rial.required' => 'The total RIAL payment is required.',
            'total_rial.numeric' => 'The total RIAL payment must be a number.',
            'total_rial.min' => 'The total RIAL payment must be at least 0.',
            'total_cad.required' => 'The total CAD payment is required.',
            'total_cad.numeric' => 'The total CAD payment must be a number.',
            'total_cad.min' => 'The total CAD payment must be at least 0.',
        ];
    }


    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    public function prepareForValidation()
    {
        // English conversion
        $convertor = new Convertor();

        $this->merge([
            'date' => $convertor->persianToEnglishDecimal($this->input('date')),
            'cad_to_usd_rate' => $convertor->persianToEnglishDecimal($this->input('cad_to_usd_rate')),
            'total_rial' => $convertor->persianToEnglishDecimal($this->input('total_rial')),
            'total_cad' => $convertor->persianToEnglishDecimal($this->input('total_cad')),
            'outfit' => $convertor->persianToEnglishDecimal($this->input('outfit')),
            'education' => $convertor->persianToEnglishDecimal($this->input('education')),
            'salary' => $convertor->persianToEnglishDecimal($this->input('salary')),
            'food' => $convertor->persianToEnglishDecimal($this->input('food')),
            'misc' => $convertor->persianToEnglishDecimal($this->input('misc')),
        ]);
    }
}
