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
    public function brand_store(): HasMany {
        return $this->hasMany(Store::class, 'brand_id');
    }
    public function brand_storeGroup(): HasMany {
        return $this->hasMany(StoreGroup::class, 'brand_id');
    }
    public function brand_product(): HasMany {
        return $this->hasMany(Product::class, 'brand_id');
    }
    public function brand_pos_category(): HasMany {
        return $this->hasMany(PosCategory::class, 'brand_id');
    }
}
