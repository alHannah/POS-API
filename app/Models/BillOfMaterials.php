<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillOfMaterails extends Model
{
    protected $table = 'bill_of_materials';
    protected $fillable = [
        'product_id',
        'bom_id',
        'qty',
        'uom_id'
    ];
}
