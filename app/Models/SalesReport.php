<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReport extends Model
{
    protected $table = "sales_reports";

    protected $fillable = [
        'reading_no',
        'store_id',
        'created_by',
        'date_created',
        'gross_sales',
        'discount',
        'less_vat_exempt',
        'vat_sales',
        'vat_amount',
        'vat_exempt',
        'rounding_adjustment',
        'start_date',
        'end_date',
        'refund'
    ];
}
