<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

/**
 * @property int $id
 * @property int $center_id
 * @property string $date
 * @property float $cad_to_usd_rate
 * @property float $total_rial
 * @property float $total_cad
 * @property float $salary
 * @property float $education
 * @property float $food
 * @property float $outfit
 * @property float $misc
 * @property string|null $misc_desc
 * @property Center $center
 */
class PaymentTransfer extends Model
{
    public $timestamps = false; 
    /**
     * @var array
     */
    protected $fillable = [
        'center_id',
        'date',
        'cad_to_usd_rate',
        'total_rial',
        'total_cad',
        'salary',
        'education',
        'food',
        'outfit',
        'misc',
        'misc_desc',
    ];

    /**
     * Define a relationship with the Center model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function center()
    {
        return $this->belongsTo('App\Models\Center');
    }
}
