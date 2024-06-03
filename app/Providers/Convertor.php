<?php

namespace App\Providers;
use File;

class Convertor {

    // English to Persian number decimals
    public function englishToPersianDecimal($number) {
        // Define the mapping of English digits to Persian digits
        $englishDigits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $persianDigits = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
    
        // Convert each English digit in the number to its Persian equivalent
        $persianNumber = str_replace($englishDigits, $persianDigits, $number);
    
        return $persianNumber;
    }

    // Persian to English number decimals
    public function persianToEnglishDecimal($number) {
        // Define the mapping of English digits to Persian digits
        $persianDigits = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        $englishDigits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    
        // Convert each English digit in the number to its Persian equivalent
        $englishNumber = str_replace($persianDigits, $englishDigits, $number);
    
        return $englishNumber;
    }

    // Jalali months convertor
    public function numberTojalaliMonth($jalaliMonthString) {
        $months = [
            'فروردین' => 1,
            'اردیبهشت' => 2,
            'خرداد' => 3,
            'تیر' => 4,
            'مرداد' => 5,
            'شهریور' => 6,
            'مهر' => 7,
            'آبان' => 8,
            'آذر' => 9,
            'دی' => 10,
            'بهمن' => 11,
            'اسفند' => 12
        ];

        return $months[$jalaliMonthString] ?? null;
    }
}