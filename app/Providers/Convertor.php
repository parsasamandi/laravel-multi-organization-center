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
        // Define the mapping of English digits to Persian digits
        $persianDigits = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        $englishDigits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    
        // Convert each English digit in the number to its Persian equivalent
        $englishNumber = str_replace($persianDigits, $englishDigits, $number);
    
        return $englishNumber;
    }

    // Jalali months convertor in dataTable
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

    // Jalali months convertor in Blade
    public function numberTojalaliMonthBlade($column) {
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