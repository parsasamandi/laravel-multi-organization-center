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
        $this->user = Auth::user();
        $this->jalaliYear = $jalaliYear;
        $this->jalaliMonth = $jalaliMonth;
    }

    public function passes($attribute, $value)
    {
        $generalInfo = GeneralInfo::where(function ($query) {
            $query->where('center_id', $this->user->id)
                ->where('jalaliMonth', $this->jalaliMonth)
                ->where('jalaliYear', $this->jalaliYear);
        })->first();

        return $generalInfo != null;
    }

    public function message()
    {
        if ($this->user->type == Center::CENTER) {
            return 'برای سال و ماه انتخاب شده، قبلا گزارش صورتحساب وارد نشده است. لطفا ابتدا گزارش صورتحساب را برای این تاریخ وارد نمایید.';

        } else {
            return 'عضو عزیز گلستان، این مرکز برای این گزارش در گذشته صورتحساب بانکی را وارد نکرده است. لطفا ابتدا گزارش صورتحساب را برای این تاریخ وارد نمایید.';
        }
    }
}
