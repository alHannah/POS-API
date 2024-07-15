<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Lumen\Auth\Authorizable;

class Users extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    public $timestamps = false;
    protected $table = "users";

    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'status',
        'email_verified_at', 'remember_token'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
        'password',
    ];

    // -------------------------BELONGS TO

    public function user_role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // -------------------------HAS MANY


    public function brand_assignment(): HasMany {
        return $this->hasMany(BrandAssignment::class, 'user_id');
    }

    public function area_assignment(): HasMany {
        return $this->hasMany(AreaAssignment::class, 'user_id');
    }
}

