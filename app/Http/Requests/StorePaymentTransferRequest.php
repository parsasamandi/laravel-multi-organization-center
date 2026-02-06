<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Morilog\Jalali\Jalalian;
use App\Providers\Convertor;
use Carbon\Carbon;


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
            'date' => ['required', 'date', 'date_format:Y-m-d'],
            'cad_to_usd_rate' => ['required', 'numeric', 'min:0'], 
            'total_rial' => ['required', 'numeric', 'min:0'], 
            'total_cad' => ['required', 'numeric', 'min:0', function($attribute, $value, $fail) {
                $totalCad = (float) $this->input('total_cad', 0);
                $sum = 
                    (float) ($this->input('operation', 0)) + 
                    (float) ($this->input('outfit', 0)) + 
                    (float) ($this->input('education', 0)) + 
                    (float) ($this->input('salary', 0)) + 
                    (float) ($this->input('food', 0)) + 
                    (float) ($this->input('misc', 0));

                // Compare with rounding to avoid precision issues
                if (round($sum, 2) !== round($totalCad, 2)) {
                    $fail("The total CAD amount ($totalCad) must equal the sum of the payment details ($sum).");
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

    public function prepareForValidation()
    {
        $convertor = new Convertor();
        
        // Handle the 'date' field separately
        if ($this->filled('date')) {
            // Try parsing the date assuming it's a Gregorian date string
            $gregorianDate = $this->input('date');

            try {
                // Convert the Gregorian date to Carbon instance
                $carbonDate = Carbon::createFromFormat('Y-m-d', $gregorianDate); 

                // Extract the Gregorian year for validation
                $year = $carbonDate->year;

                // Validate that the year is within the correct range
                if ($year < 1000 || $year > 3000) {
                    throw new \InvalidArgumentException("The year must be between 1000 and 3000.");
                }

                // Convert the Gregorian date to Jalali using Morilog\Jalali
                $jalaliDate = Jalalian::fromCarbon($carbonDate)->format('Y-m-d');

                // Apply Persian-to-English decimal conversion on the Jalali date (if needed)
                $jalaliDateConverted = $convertor->persianToEnglishDecimal($jalaliDate);

                // Merge the converted Jalali date back into the request data
                $this->merge(['date' => $jalaliDateConverted]);

            } catch (\Exception $e) {
                // Handle invalid date format or conversion errors gracefully
                throw new \InvalidArgumentException("Invalid Gregorian date format. Please use 'YYYY-MM-DD'.");
            }
        }
        
        // Convert other numeric fields using the Convertor
        $this->merge([
            'cad_to_usd_rate' => $convertor->persianToEnglishDecimal($this->input('cad_to_usd_rate', 0)),
            'total_rial' => $convertor->persianToEnglishDecimal($this->input('total_rial', 0)),
            'total_cad' => $convertor->persianToEnglishDecimal($this->input('total_cad', 0)),
            'operation' => $convertor->persianToEnglishDecimal($this->input('operation', 0)),
            'outfit' => $convertor->persianToEnglishDecimal($this->input('outfit', 0)),
            'education' => $convertor->persianToEnglishDecimal($this->input('education', 0)),
            'salary' => $convertor->persianToEnglishDecimal($this->input('salary', 0)),
            'food' => $convertor->persianToEnglishDecimal($this->input('food', 0)),
            'misc' => $convertor->persianToEnglishDecimal($this->input('misc', 0)),
        ]);
    }


}