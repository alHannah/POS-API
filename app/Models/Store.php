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

    public function store_brands () : BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function store_with_schedule () : HasMany {
        return $this->hasMany(StorePerSchedule::class, 'store_id');
    }

    public function store_per_area () : BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function store_per_assignments () : HasMany
    {
        return $this->HasMany(StoreAssignment::class, 'store_id');
    }

    public function group_per_store () : BelongsTo
    {
        return $this->belongsTo(StoreGroup::class, 'group_id');
    }

    public function store_price_tier () : BelongsTo
    {
        return $this->belongsTo(PriceTier::class, 'tier_id');
    }

    public function store_devices () : HasMany
    {
        return $this->hasMany(Device::class, 'store_id', 'id');
    }

    public function store_oic () : HasMany
    {
        return $this->hasMany(OicPerStore::class,'store_id', 'id');
    }

    public function store_product_per_store () : HasMany
    {
        return $this->hasMany(ProductPerStore::class,'store_id', 'id');
    }

    public function store_general_time(): HasMany {
        return $this->hasMany(GeneralTimeSetting::class, 'store_id');

    }

    public function store_manager () : HasMany
    {
        return $this->hasMany(ManagerInCharge::class,'store_id', 'id');
    }
}
