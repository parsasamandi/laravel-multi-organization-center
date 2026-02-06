<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

/**
 * @property int $id
 * @property string $name
 * @property GeneralInfo $generalInfo
 * @property Report $report
 */
class Center extends Model implements Authenticatable
{
    use AuthenticatableTrait;
    use CascadesDeletes;

    public $timestamps = false;

    const CENTER = 0;
    const GOLESTANTEAM = 1;

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'centers';

    /**
     * Cascade On Delete.
     */
    protected $cascadeDeletes = ['generalInfo', 'reports', 'statuses', 'paymentTransfers'];

    /**
     * @var array
     */
    protected $fillable = [
        'code', 'name', 'name_en', 'phone_number', 'email', 'type', 'password',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function generalInfo()
    {
        return $this->hasMany('App\Models\GeneralInfo', 'center_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reports()
    {
        return $this->hasMany('App\Models\Report', 'center_id');
    }

     /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paymentTransfers()
    {
        return $this->hasMany('App\Models\PaymentTransfer', 'center_id');
    }

    /*
     * Get all of the center's statuses.
     */
    public function statuses()
    {
        return $this->morphOne('App\Models\Status', 'status');
    }
}
