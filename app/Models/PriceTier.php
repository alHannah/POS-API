<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceTier extends Model
{
    protected $table = "price_tiers";
    protected $fillable = [
        'name',
        'status',
        'sales_channel',
        'mop_id',
        'brand_id'
    ];


}
