<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    protected $table = "access_tokens";
    protected $fillable = [
        'mobile_user_id',
        'store_id',
        'token'
    ];
}
