<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Areas extends Model
{
    protected $table = 'areas';

    protected $fillable = [
        'name',
        'status',
        'brand_id'
    ];

    public function brand_per_area () : BelongsTo 
    {
        return $this->belongsTo(Brands::class, 'brand_id');
    }
}
