<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPerStores extends Model
{
    protected $table = "product_per_stores";

    protected $fillable = [
        'product_id',
        'store_id',
        'status'
    ];
}
