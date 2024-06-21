<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UomCategory extends Model
{
    protected $table = "uom_categories";
    protected $fillable = [
        'name'
    ];
}
