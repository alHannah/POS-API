<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Packaging extends Model
{
    protected $table = "packagings";
    protected $fillable = [
        'product_id',
        'order_type_id'
    ];


}
