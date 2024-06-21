<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Remark extends Model
{
    protected $table = "remarks";

    protected $fillable = [
        'remarks',
    ];
}
