<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        return $this->belongsTo('App\Models\Center');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function generalInfo()
    {
        return $this->belongsTo('App\Models\GeneralInfo');
    }
}
