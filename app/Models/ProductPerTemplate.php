<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPerTemplate extends Model
{
    protected $table = "product_per_template";
    protected $fillable = [
        'template_id',
        'product_id',
        'uom_id',
        'qty'
    ];
}
