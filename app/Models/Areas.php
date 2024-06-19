<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Areas extends Model
{
    protected $table = 'areas';

    protected $fillable = [
        'name',
        'status',
        'brand_id'
    ];
}
