<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricePerTier extends Model 
{
    protected $table = "price_per_tier";
    public $timestamps = false;
    protected $fillable = [
        'product_id',
        'tier_id',
        'price'
    ];


}