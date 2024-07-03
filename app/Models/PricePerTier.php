<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PricePerTier extends Model
{
    protected $table = "price_per_tier";
    protected $fillable = [
        'product_id',
        'tier_id',
        'price'
    ];

    public function price_per_tier_per_store () : BelongsTo
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function price_per_tier_per_tiers () : BelongsTo
    {
        return $this->belongsTo(PriceTier::class,'tier_id');
    }

}
