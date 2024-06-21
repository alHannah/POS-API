<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
