<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosCategorie extends Model
{
    protected $table = "pos_categories";
    protected $fillable = [
        'pos_category_name',
        'status',
        'brand_id'
    ];

    public function posCategory_per_product () : HasMany
    {
        return $this->hasMany(Product::class,'pos_category_id', 'id');
    }
}
