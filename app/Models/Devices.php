<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devices extends Model
{
    protected $table = 'devices';

    protected $fillable = [
        'device_id',
        'store_id',
        'status'
    ];
}
