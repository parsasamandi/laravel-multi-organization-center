<?php

namespace App\Providers;
use File;

class EnglishConvertion {

    // Persian to English number decimals
    public function convert($number) {
        // Define the mapping of English digits to Persian digits
        $persianDigits = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        $englishDigits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    
        // Convert each English digit in the number to its Persian equivalent
        $englishNumber = str_replace($persianDigits, $englishDigits, $number);
    
        return $englishNumber;
    }
}