<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $table = "purchase_details";

    protected $fillable = [
        'purchase_id',
        'product_id',
        'qty',
        'uom_id',
        'product_status',
    ];
}
