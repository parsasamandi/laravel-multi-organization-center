<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;


/**
 * @property integer $id
 * @property int $center_id
 * @property int $general_info_id
 * @property float $expenses
 * @property float $range
 * @property float $receipt
 * @property string $description
 * @property int $type
 * @property Center $center
 * @property GeneralInfo $generalInfo
 */
class Report extends Model
{

    const EMPLOYEE = 0;
    const EDUCATION = 1;
    const HEALTHCARE = 2;

    public $timestamps = false;

    /**
     * Cascade On Delete.
     */
    use CascadesDeletes;
    protected $cascadeDeletes = ['statuses'];


    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['center_id', 'general_info_id', 'expenses', 'range', 'receipt', 'description', 'type'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function center()
    {
        return $this->belongsTo('App\Models\Center', 'center_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function generalInfo()
    {
        return $this->belongsTo('App\Models\GeneralInfo', 'general_info_id');
    }

    /*
     * Get all of the report's status.
     */
    public function statuses() {
        return $this->morphOne('App\Models\Status', 'status');
    }
}
