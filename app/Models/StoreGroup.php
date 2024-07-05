<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreGroup extends Model
{
    protected $table = "store_groups";
    protected $fillable = [
        'group_name',
        'brand_id',
    ];

    public function storeGroup_store(): HasMany
    {
        return $this->HasMany(Store::class, 'group_id', 'id');
    }
    public function storeGroup_brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
