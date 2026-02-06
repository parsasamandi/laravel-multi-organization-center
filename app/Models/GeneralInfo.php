<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property string $bank_statement_receipt
 * @property float $bank_balance
 * @property string $date
 * @property Center $center
 * @property Report $report
 */
class GeneralInfo extends Model
{
    public $timestamps = false;
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'general_infos';


    /**
     * Cascade On Delete.
     */
    use CascadesDeletes;
    protected $cascadeDeletes = ['statuses', 'reports'];

    /**
     * @var array
     */
    protected $fillable = ['center_id', 'bank_statement_receipt', 'bank_balance', 'jalaliMonth', 'jalaliYear'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function center()
    {
        return $this->belongsTo('App\Models\Center', 'center_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function reports()
    {
        return $this->hasMany('App\Models\Report', 'general_info_id');
    }

    /*
     * Get all of the general info's status.
     */
    public function statuses() {
        return $this->morphOne('App\Models\Status', 'status');
    }
}
