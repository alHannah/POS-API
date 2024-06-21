<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotifToken extends Model
{
    protected $table = "notif_tokens";
    protected $fillable = [
        'token',
        'mobile_user_id',
        'store_id'
    ];


}
