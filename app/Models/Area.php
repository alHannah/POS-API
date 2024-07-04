<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $table = 'areas';
    protected $fillable = [
        'name',
        'status',
        'brand_id',
    ];
    public function areas()
    {
        return $this->hasManyThrough(Store::class, AreaAssignment::class, 'area_id', 'id', 'id', 'store_id');
    }

    public function brand_areas(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function areas_per_area_assignment(): HasMany
    {
        return $this->hasMany(AreaAssignment::class, 'area_id');
    }

    public function areas_per_stores(): HasMany
    {
        return $this->hasMany(Store::class, 'area_id');
    }
}
