<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UomCategories extends Model
{
    protected $table = "uom_categories";
    public $timestamps = false;
    protected $fillable = [
        'name'
    ];
}
