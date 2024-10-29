<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function inventory_stores(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    public function inventory_users(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'user_id');
    }

    // ---------------------------HAS MANY

    public function inventory_details(): HasMany
    {
        return $this->hasMany(InventoryDetail::class, 'inventory_id');
    }
}
