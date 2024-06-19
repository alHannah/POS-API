<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileUsers extends Model
{
    protected $table = 'mobile_users';

    protected $fillable = [
        'name',
        'username',
        'email_verified_at',
        'password',
        'level',
        'status',
        'verification_code',
        'login_status',
        'store_id',
        'remember_token'
    ];
}
