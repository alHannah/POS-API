<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralTimeSetting extends Model
{
    protected $table = 'general_time_setting';
    protected $fillable = [
        'store_id',
        'start_time',
        'end_time'
    ];

    public function  general_time_store() : BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}
