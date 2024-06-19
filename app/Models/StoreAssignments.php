<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreAssignments extends Model
{
    protected $table = "store_assignments";

    protected $fillable = [
        'mobile_user_id',
        'store_id',
        'start_date',
        'end_date'
    ];
}
