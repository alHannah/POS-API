<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'name',
        'tag',
        'status'
    ];

    public function category_product () : HasMany
    {
        return $this->hasMany(Product::class,'category_id', 'id');
    }
}
