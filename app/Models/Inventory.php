<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $table = 'inventory';
    protected $fillable = [
        'user_id',
        'store_id',
        'date_submitted',
        'status',

    ];
    // ---------------------------BELONGS TO

    public function inventory_stores () : BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    // ---------------------------HAS MANY
}
