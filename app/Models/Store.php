<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $table = "stores";
    protected $fillable = [
        'store_name',
        'store_code',
        'store_address',
        'tin',
        'vat_type',
        'area_id',
        'group_id',
        'status',
        'tier_id',
        'tablet_serial_no',
        'brand_id',
        'pos_enabled'
    ];

    /*public function brand_per_area () : BelongsTo
    {
        return $this->belongsTo(Brands::class, 'brand_id');
    }*/

    public function store_with_schedule () : HasMany {
        return $this->hasMany(StorePerSchedules::class, 'store_id');
    }

    public function store_per_area () : BelongsTo
    {
        return $this->belongsTo(Areas::class, 'area_id');
    }

    public function store_per_assignments () : HasMany
    {
        return $this->HasMany(StoreAssignments::class, 'store_id');
    }

    public function store_per_group () : BelongsTo
    {
        return $this->belongsTo(StoreGroups::class, 'group_id');
    }
}
