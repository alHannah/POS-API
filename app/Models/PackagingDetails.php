<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackagingDetails extends Model 
{
    protected $table = "packaging_details";
    public $timestamps = false;
    protected $fillable = [
        'packaging_id',
        'product_id',
        'qty',
        'uom_id'
    ];


}