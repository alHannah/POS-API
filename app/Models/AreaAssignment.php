<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaAssignment extends Model
{
    protected $table = 'area_assignments';

    protected $fillable = [
        'area_id',
        'user_id'
    ];
}
