<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessTokens extends Model
{
    protected $table = "access_tokens";
    public $timestamps = false;
    protected $fillable = [
        'mobile_user_id',
        'store_id',
        'token'
    ];
}
