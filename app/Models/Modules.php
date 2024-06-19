<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modules extends Model 
{
    protected $table = "modules";
    public $timestamps = false;
    protected $fillable = [
        'module_name',
        'add',
        'edit',
        'view',
        'delete',
        'approve'
    ];


}