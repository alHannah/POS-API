<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Areas extends Model
{
    protected $table = 'areas';

    protected $fillable = [
        'name',
        'status',
        'brand_id'
    ];

    public function brand_per_area () : BelongsTo
    {
        return $this->belongsTo(Brands::class, 'brand_id');
    }

    public function areas_per_area_assignment() : HasMany
    {
        return $this->hasMany(AreaAssignment::class, 'area_id');
    }

    public function areas_per_stores() : HasMany
    {
        return $this->hasMany(Store::class, 'area_id');
    }
}
