<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function price_tier_store () : HasMany
    {
        return $this->hasMany(Store::class, 'tier_id');
    }

    public function price_tier_per_price () : HasMany
    {
        return $this->hasMany(PricePerTier::class, 'tier_id');
    }

    public function price_tier_per_brand () : BelongsTo
    {
        return $this->belongsTo(PricePerTier::class, 'brand_id');
    }
}
