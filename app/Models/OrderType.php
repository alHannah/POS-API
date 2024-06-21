<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderType extends Model
{
    protected $table = "order_types";
    protected $fillable = [
        'name',
        'is_default',
        'status'
    ];


}
