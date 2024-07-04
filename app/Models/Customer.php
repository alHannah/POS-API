<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    protected $table = 'customers';
    protected $fillable = [
        'name',
        'created_by',
        'store_id',
        'number',
        'email'
    ];
    // ---------------------------BELONGS TO

    public function customer_store () : BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    // ---------------------------HAS MANY
}
