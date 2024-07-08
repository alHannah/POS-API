<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Packaging extends Model
{
    protected $table = "packagings";
    protected $fillable = [
        'product_id',
        'order_type_id'
    ];

    public function packaging_per_detail () : HasMany
    {
        return $this->hasMany(PackagingDetail::class,'packaging_id');
    }
}
