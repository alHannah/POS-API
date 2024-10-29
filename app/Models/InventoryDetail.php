<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryDetail extends Model
{
    protected $table = 'inventory_details';
    protected $fillable = [
        'inventory_id',
        'product_id',
        'uom_id',
        'qty',
        'delivered',
        'usage',
        'pullouts',
        'ending',
        'refund'
    ];

    public function inventoryDetails_uom(): BelongsTo
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }
    public function inventoryDetails_product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
