<?php

namespace App\Providers;
use Morilog\Jalali\Jalalian;
use Aws\S3\S3Client;
use File;

class Convertor {

    // English to Persian number decimals
    public function englishToPersianDecimal($number) {
        $translationTable = [
            '0' => '۰', 
            '1' => '۱', 
            '2' => '۲', 
            '3' => '۳', 
            '4' => '۴', 
            '5' => '۵', 
            '6' => '۶', 
            '7' => '۷', 
            '8' => '۸', 
            '9' => '۹'
        ];

        return strtr($number, $translationTable);
    }

    // Persian to English number decimals
    public function persianToEnglishDecimal($number) {
        $translationTable = [
            '۰' => '0', 
            '۱' => '1', 
            '۲' => '2', 
            '۳' => '3', 
            '۴' => '4', 
            '۵' => '5', 
            '۶' => '6', 
            '۷' => '7', 
            '۸' => '8', 
            '۹' => '9'
        ];
    
        return strtr($number, $translationTable);
    }

    // Jalali months convertor
    public function convertJalaliMonth($value = null) {
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

        if ($value === null) {
            return $months; 
        }
    
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

    public function jalaliYearDropdown()
    {
        // Get the current Jalali year
        $currentYear = Jalalian::now()->getYear();
        
        // Define the start year
        $startYear = 1402;
    
        // Generate the dropdown HTML
        $dropdown = '';
        for ($year = $startYear; $year <= $currentYear; $year++) {
            $persianYear = $this->englishToPersianDecimal($year);
            $dropdown .= '<option value=\'' . $year . '\'>' . $persianYear . '</option>';
        }
    
        return $dropdown;
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
        
        // URL encode the filename to handle non-ASCII characters
        $encodedFileName = rawurlencode($fileName);

        try {
            $cmd = $s3Client->getCommand('GetObject', [
                'Bucket' => $bucket,
                'Key'    => $filePath,
                'ResponseContentDisposition' => 'attachment; filename="' . $encodedFileName . '"',
            ]);

            $request = $s3Client->createPresignedRequest($cmd, '+1 hour');
            return (string) $request->getUri();
        } catch (AwsException $e) {
            // Handle the error
            return null;
        }
    }

}