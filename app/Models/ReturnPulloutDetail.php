<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnPulloutDetail extends Model
{
    protected $table = "return_pullouts_details";

    protected $fillable = [
        'return_id',
        'product_id',
        'uom_id',
        'qty',
        'status',
        'remarks'
    ];
}
