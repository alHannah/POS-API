<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $table = 'brands';
    protected $fillable = [
        'brand',
        'status'
    ];

    public function brand_areas(): HasMany {
        return $this->hasMany(Area::class, 'brand_id');
    }
}
