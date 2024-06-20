<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
