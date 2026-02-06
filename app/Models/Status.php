<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $status
 * @property int $status_id
 * @property string $status_type
 */
class Status extends Model
{

    const NOTCONFIRMED = 0;
    const SUCCESSFUL = 1;
    const UNSUCCESSFUL = 2;

    public $timestamps = false;

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'statuses';

    /**
     * @var array
     */
    protected $fillable = ['status', 'status_id', 'status_type'];

    /**
     * Get The parent status model
     */
    public function status() {
        return $this->morphTo();
    }
}
