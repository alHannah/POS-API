<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManagerInCharge extends Model
{
    protected $table = 'manager_in_charges';
    protected $fillable = [
        'store_id',
        'user_id'
    ];
}
