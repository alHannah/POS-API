<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalAccessTokens extends Model
{
    protected $table = "personal_access_tokens";
    public $timestamps = false;
    protected $fillable = [
        'tokenable_type',
        'tokenable_id',
        'name',
        'token',
        'abilities',
        'last_used_at'
    ];


}
