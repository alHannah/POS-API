<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnPullouts extends Model
{
    protected $table = "return_pullouts";
    protected $fillable = [
        'user_id',
        'store_id',
        'ref_no',
        'date_submitted',
        'date_created',
        'status',
        'approved_by',
        'approved_date'
    ];
}
