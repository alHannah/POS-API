<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTypes extends Model 
{
    protected $table = "order_types";
    public $timestamps = false;
    protected $fillable = [
        'name',
        'is_default',
        'status'
    ];


}