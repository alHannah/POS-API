<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosCategory extends Model
{
    protected $table = "pos_categories";
    protected $fillable = [
        'pos_category_name',
        'status',
        'brand_id'
    ];

    public function posCategory_per_product () : HasMany
    {
        return $this->hasMany(Product::class,'pos_category_id', 'id');
    }

    public function pos_category_brand() : BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function pos_category_product () : HasMany
    {
        return $this->hasMany(Product::class, 'pos_category_id');
    }

}
