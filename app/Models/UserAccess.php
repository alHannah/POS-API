<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAccess extends Model
{
    protected $table = "user_accesses";
    protected $fillable = [
        'role_id',
        'module_id',
        'add',
        'edit',
        'view',
        'delete',
        'approve'
    ];

    public function user_access_module(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function user_access_role(): BelongsTo
    {
        return $this->belongsTo (Role::class, 'role_id');
    }
}
