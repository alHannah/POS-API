<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OicPerStores extends Model 
{
    protected $table = "oic_per_stores";
    public $timestamps = false;
    protected $fillable = [
        'mobile_user_id',
        'store_id'
    ];


}