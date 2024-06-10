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
     * @var array
     */
    protected $fillable = [
        'code', 'name', 'phone_number', 'email', 'type', 'password',
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
        return $this->hasOne('App\Models\Report', 'center_id');
    }

    /*
     * Get all of the course's status.
     */
    public function statuses() {
        return $this->morphOne('App\Models\Status', 'status');
    }
}
