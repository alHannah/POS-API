<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceTiers extends Model 
{
    protected $table = "price_tiers";
    public $timestamps = false;
    protected $fillable = [
        'name',
        'status',
        'sales_channel',
        'mop_id',
        'brand_id'
    ];


}