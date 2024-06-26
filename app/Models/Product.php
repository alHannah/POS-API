<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $table = "products";

    protected $fillable = [
        'name',
        'product_code',
        'category_id',
        'product_classification_id',
        'pos_category_id',
        'uom_id',
        'min_level_uom',
        'product_tag',
        'product_image',
        'for_packaging',
        'brand_id',
        'status'
    ];

    public function products_product_per_store () : HasMany
    {
        return $this->hasMany(ProductPerStore::class,'product_id', 'id');
    }

    public function product_category () : BelongsTo
    {
        return $this->belongsTo(Category::class,'category_id', 'id');
    }
}
