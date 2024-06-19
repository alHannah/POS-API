<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersAccesses extends Model
{
    protected $table = "users_accesses";
    public $timestamps = false;
    protected $fillable = [
        'role_id',
        'module_id',
        'add',
        'edit',
        'view',
        'delete',
        'approve'
    ];
}
