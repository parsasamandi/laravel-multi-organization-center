<?php

namespace App\Http\Requests\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Center;
use App\Models\GeneralInfo;
use Auth;

class GeneralInfoExists implements Rule
{
    protected $center;
    protected $jalaliYear;
    protected $jalaliMonth;

    public function __construct($jalaliYear, $jalaliMonth)
    {
        $this->center = Auth::user();
        $this->jalaliYear = $jalaliYear;
        $this->jalaliMonth = $jalaliMonth;
    }

    public function passes($attribute, $value)
    {
        $generalInfo = GeneralInfo::where(function ($query) {
            if ($this->center->type == Center::CENTER) {
                $query->where('center_id', $this->center->id)
                      ->where('jalaliMonth', $this->jalaliMonth)
                      ->where('jalaliYear', $this->jalaliYear);
            }
            $query->where('jalaliMonth', $this->jalaliMonth)
                ->where('jalaliYear', $this->jalaliYear);
        })->first();

        return $generalInfo != null;
    }

    public function message()
    {
        return 'برای سال و ماه انتخاب شده، قبلا گزارش صورتحساب وارد نشده است. لطفا ابتدا گزارش صورتحساب را برای این تاریخ وارد نمایید.';
    }
}
