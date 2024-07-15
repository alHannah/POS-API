<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    protected $table = "modules";
    protected $fillable = [
        'module_name',
        'add',
        'edit',
        'view',
        'delete',
        'approve'
    ];


    // -----------------BOLONGS TO



    // -----------------HAS MANY

    public function module_userAccess(): HasMany
    {
        return $this->hasMany(UserAccess::class);
    }

}
