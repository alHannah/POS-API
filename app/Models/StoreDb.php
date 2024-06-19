<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreDb extends Model
{
    protected $table = "store_db";
    public $timestamps = false;
    protected $fillable = [
        'store_id',
        'filename'
    ];
}
