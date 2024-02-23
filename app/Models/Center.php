<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

/**
 * @property int $id
 * @property string $name
 * @property GeneralInfo $generalInfo
 * @property Report $report
 */
class Center extends Model implements Authenticatable
{
    use AuthenticatableTrait;

    public $timestamps = false;

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'centers';

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'phone_number', 'email', 'password',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function generalInfo()
    {
        return $this->hasOne('App\Models\GeneralInfo', 'center_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function report()
    {
        return $this->hasOne('App\Report', 'center_id');
    }
}
