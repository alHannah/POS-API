<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhysicalCountDetail extends Model
{
    protected $table = "physical_count_details";
    protected $fillable = [
        'pc_id',
        'product_id',
        'uom_id',
        'inventory_qty',
        'system_qty',
        'qty'
    ];


}
