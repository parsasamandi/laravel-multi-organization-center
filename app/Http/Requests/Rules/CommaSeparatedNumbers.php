<?php

namespace App\Http\Requests\Rules;

use Illuminate\Contracts\Validation\Rule;

class CommaSeparatedNumbers implements Rule
{
    /**
     * Determine if the value is valid.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Allow empty string (optional)
        if (empty($value)) {
            return true;
        }

        // Check for comma-separated format
        if (!preg_match('/^[0-9,]+$/', $value)) {
            return false;
        }

        // Validate individual numbers (optional)
        $numbers = explode(',', $value);
        foreach ($numbers as $number) {
            if (!is_numeric($number)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @param  string  $attribute
     * @return string
     */
    public function message($attribute)
    {
        return 'The :attribute field must be a comma-separated list of numbers.';
    }
}
