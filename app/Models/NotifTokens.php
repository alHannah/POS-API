<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotifTokens extends Model 
{
    protected $table = "notif_tokens";
    public $timestamps = false;
    protected $fillable = [
        'token',
        'mobile_user_id',
        'store_id'
    ];


}