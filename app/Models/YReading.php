<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YReading extends Model
{
    protected $table = "y_readings";
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
}
