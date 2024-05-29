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
    public function numberTojalaliMonth($column) {
        switch ($column) {
            case 1:
                return 'فروردین';
                break;
            case 2:
                return 'اردیبهشت';
                break;
            case 3:
                return 'خرداد';
                break;
            case 4:
                return 'تیر';
                break;
            case 5:
                return 'مرداد';
                break;
            case 6:
                return 'شهریور';
                break;
            case 7:
                return 'مهر';
                break;
            case 8:
                return 'آبان';
                break;
            case 9:
                return 'آذر';
                break;
            case 10:
                return 'دی';
                break;
            case 11:
                return 'بهمن';
                break;
            case 12:
                return 'اسفند';
                break;
        }
    }
    
}