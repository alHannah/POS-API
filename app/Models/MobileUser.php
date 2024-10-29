<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MobileUser extends Model
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
    // ---------------------------BELONGS TO

    public function mobileUser_stores () : BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    // ---------------------------HAS MANY

    public function mobileUser_returnPullouts () : HasMany
    {
        return $this->hasMany(ReturnPullout::class, 'user_id');
    }
}
