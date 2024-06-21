<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricePerTier extends Model
{
    protected $table = "price_per_tier";
    protected $fillable = [
        'product_id',
        'tier_id',
        'price'
    ];


}
