<?php

namespace App\Providers;
use Aws\S3\S3Client;
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
        // Define the mapping of Persian digits to English digits
        $persianDigits = '۰۱۲۳۴۵۶۷۸۹';
        $englishDigits = '0123456789';
    
        // Create a translation table for Persian to English digit conversion
        $translationTable = array_flip(mb_str_split($persianDigits));
        
        // Convert Persian digits to English using the translation table
        $englishNumber = strtr($number, $translationTable);
    
        return $englishNumber;
    }

    // Jalali months convertor
    public function convertJalaliMonth($value) {
        // Define the mapping between Persian month names and their corresponding numbers
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
    
        if (is_string($value)) {
            // Convert Persian month name to integer
            return $months[$value] ?? null;
        } elseif (is_int($value)) {
            // Convert integer to Persian month name
            $flippedMonths = array_flip($months); // Reverse the array to map integers to month names
            return $flippedMonths[$value] ?? null;
        }
    
        return null; // Return null if the input is neither a string nor an integer
    }

    // Download the url with its original name stored in the database with an hour time limit.
    public function getPresignedUrlWithContentDisposition($filePath, $fileName)
    {
        $s3Client = new S3Client([
            'version' => 'latest',
            'region'  => config('filesystems.disks.s3.region'),
            'credentials' => [
                'key'    => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);

        $bucket = config('filesystems.disks.s3.bucket');
        $cmd = $s3Client->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key'    => $filePath,
            'ResponseContentDisposition' => 'attachment; filename="' . $fileName . '"',
        ]);

        $request = $s3Client->createPresignedRequest($cmd, '+1 hour');
        return (string) $request->getUri();
    }

}