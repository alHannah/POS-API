<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosCategories extends Model 
{
    protected $table = "pos_categories";
    public $timestamps = false;
    protected $fillable = [
        'pos_category_name',
        'status',
        'brand_id'
    ];


}