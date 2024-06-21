<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryDetails extends Model
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
}
