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

    // ---------------------------BELONGS TO

    public function store_brands () : BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    public function store_per_area () : BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
    public function store_storeGroup () : BelongsTo
    {
        return $this->belongsTo(StoreGroup::class, 'group_id');
    }
    public function store_price_tier () : BelongsTo
    {
        return $this->belongsTo(PriceTier::class, 'tier_id');
    }

    // ---------------------------HAS MANY

    public function store_with_schedule () : HasMany {
        return $this->hasMany(StorePerSchedule::class, 'store_id');
    }
    public function store_per_assignments () : HasMany
    {
        return $this->HasMany(StoreAssignment::class, 'store_id');
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
    public function store_zReading () : HasMany
    {
        return $this->hasMany(ZReading::class,'store_id', 'id');
    }
    public function store_transaction () : HasMany
    {
        return $this->hasMany(Transaction::class,'store_id', 'id');
    }
    public function store_cashReport () : HasMany
    {
        return $this->hasMany(CashReport::class,'store_id', 'id');
    }
    public function store_receiving () : HasMany
    {
        return $this->hasMany(Receiving::class,'store_id', 'id');
    }
    public function store_template () : HasMany
    {
        return $this->hasMany(Template::class,'store_id', 'id');
    }
    public function store_inventory () : HasMany
    {
        return $this->hasMany(Inventory::class,'store_id', 'id');
    }
    public function store_returnPullout () : HasMany
    {
        return $this->hasMany(ReturnPullout::class,'store_id', 'id');
    }
    public function store_physicalCount () : HasMany
    {
        return $this->hasMany(PhysicalCount::class,'store_id', 'id');
    }
    public function store_purchase () : HasMany
    {
        return $this->hasMany(Purchase::class,'store_id', 'id');
    }
    public function store_xReadings () : HasMany
    {
        return $this->hasMany(XReading::class,'store_id', 'id');
    }
    public function store_salesReport () : HasMany
    {
        return $this->hasMany(SalesReport::class,'store_id', 'id');
    }
    public function store_mobileUser () : HasMany
    {
        return $this->hasMany(MobileUser::class,'store_id', 'id');
    }
    public function store_uomCategory () : HasMany
    {
        return $this->hasMany(UomCategory::class,'store_id', 'id');
    }
    public function store_billOfMaterial () : HasMany
    {
        return $this->hasMany(BillOfMaterial::class,'store_id', 'id');
    }
    public function store_notifToken () : HasMany
    {
        return $this->hasMany(NotifToken::class,'store_id', 'id');
    }
    public function store_customers () : HasMany
    {
        return $this->hasMany(Customer::class,'store_id', 'id');
    }
}
