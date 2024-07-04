<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZReading extends Model
{
    protected $table = "z_readings";
    protected $fillable = [
        'reading_no',
        'store_id',
        'created_by',
        'gross_sales',
        'discount',
        'less_vat_exempt',
        'vat_sales',
        'vat_amount',
        'vat_exempt',
        'rounding_adjustment',
        'refund'
    ];
    // ---------------------------BELONGS TO

    public function zReading_stores () : BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    // ---------------------------HAS MANY
}
