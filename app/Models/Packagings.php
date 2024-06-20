<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Packagings extends Model 
{
    protected $table = "packagings";
    public $timestamps = false;
    protected $fillable = [
        'product_id',
        'order_type_id'
    ];


}