<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackagingDetail extends Model
{
    protected $table = "packaging_details";
    protected $fillable = [
        'packaging_id',
        'product_id',
        'qty',
        'uom_id'
    ];


}
