<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductClassifications extends Model 
{
    protected $table = "product_classifications";
    public $timestamps = false;
    protected $fillable = [
        'name',
        'status'
    ];


}