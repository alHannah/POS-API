<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';
    protected $fillable = [
        'user_id',
        'store_id',
        'date_submitted',
        'status',

    ];
}
