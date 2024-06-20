<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAccesses extends Model
{
    protected $table = "user_accesses";
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

    public function user_access_module() : BelongsTo
    {
        return $this->belongsTo(Modules::class, 'module_id');
    }
}
