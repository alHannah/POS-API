<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
