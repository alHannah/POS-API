<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandAssignments extends Model
{
    protected $table = 'brand_assignments';

    protected $fillable = [
        'brand_id',
        'user_id',
    ];
}
