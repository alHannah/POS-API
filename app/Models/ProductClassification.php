<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductClassification extends Model
{
    protected $table = "product_classifications";
    protected $fillable = [
        'name',
        'status'
    ];


}
