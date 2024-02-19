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

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'center';

    /**
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function generalInfo()
    {
        return $this->hasOne('App\GeneralInfo', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function report()
    {
        return $this->hasOne('App\Report', 'center_id');
    }
}
