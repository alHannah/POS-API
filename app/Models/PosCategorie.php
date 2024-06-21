<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosCategorie extends Model
{
    protected $table = "pos_categories";
    protected $fillable = [
        'pos_category_name',
        'status',
        'brand_id'
    ];


}
