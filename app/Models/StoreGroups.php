<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreGroups extends Model
{
    protected $table = "store_groups";
    public $timestamps = false;
    protected $fillable = [
        'group_name',
        'brand_id'
    ];
}
