<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $table = "transactions";
    public $timestamps = false;
    protected $fillable = [
        'store_id',
        'submitted_by',
        'order_number',
        'refund_number',
        'void_number',
        'return_number',
        'cancel_number',
        'reference_number',
        'customer_number',
        'customer_id',
        'subtotal',
        'discount_id',
        'discount_amt',
        'vat',
        'vat_amt',
        'total',
        'discount_subtotal',
        'vat_exempt',
        'vat_twelve',
        'cash_payment',
        'customer_change',
        'transaction_date',
        'settlement_type',
        'manager_id'
    ];
}
