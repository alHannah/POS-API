<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhysicalCount extends Model
{
    protected $table = "physical_count";
    protected $fillable = [
        'user_id',
        'store_id',
        'inventory_no',
        'date_submitted',
        'remarks',
        'approved_id',
        'created_by',
        'date_approved',
        'date_created',
        'status',
        'mi_tag'
    ];


}
