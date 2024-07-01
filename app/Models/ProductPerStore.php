<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPerStore extends Model
{
    protected $table = "product_per_stores";

    protected $fillable = [
        'product_id',
        'store_id',
        'status'
    ];

    public function products_product_per_store () : BelongsTo
    {
        return $this->belongsTo(Product::class,'product_id', 'id');
    }

    public function store_product_per_store () : BelongsTo
    {
        return $this->belongsTo(Store::class,'store_id', 'id');
    }
}
