<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreGroup extends Model
{
    protected $table = "store_groups";
    protected $fillable = [
        'id',
        'group_name',
        'brand_id',
        'created_at'
    ];

    public function group_per_store(): HasMany
    {
        return $this->HasMany(Store::class, 'group_id');
    }
    public function storeGroup_brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    public function brand_storeGroup(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
