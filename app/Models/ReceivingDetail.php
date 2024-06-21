<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceivingDetail extends Model
{
    protected $table = "receiving_details";

    protected $fillable = [
        'receiving_id',
        'product_id',
        'uom_id',
        'qty',
        'received_qty',
        'validated_qty',
        'product_status',
        'remarks'
    ];
}
