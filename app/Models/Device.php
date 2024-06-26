<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Device extends Model
{
    protected $table = 'devices';
    protected $fillable = [
        'device_id',
        'store_id',
        'status'
    ];

    public function device_per_store () : BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}
