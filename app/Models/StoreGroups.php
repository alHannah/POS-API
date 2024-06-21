<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreGroups extends Model
{
    protected $table = "store_groups";
    public $timestamps = false;
    protected $fillable = [
        'group_name',
        'brand_id'
    ];

    public function group_per_store(): HasMany
    {
        return $this->HasMany(Store::class, 'group_id');
    }
}
