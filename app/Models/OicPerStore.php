<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OicPerStore extends Model
{
    protected $table = "oic_per_stores";
    protected $fillable = [
        'mobile_user_id',
        'store_id'
    ];


}
