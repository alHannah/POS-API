<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $table = "roles";

    protected $fillable = [
        'role_name'
    ];

    // -----------------BOLONGS TO



    // -----------------HAS MANY

    public function role_userAccess(): HasMany
    {
        return $this->hasMany(UserAccess::class);
    }

    public function role_users(): HasMany
    {
        return $this->hasMany(Users::class);
    }
}
