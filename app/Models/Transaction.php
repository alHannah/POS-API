<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $table = "transactions";
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

    // ---------------------------BELONGS TO
    public function transaction_stores () : BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    // ---------------------------HAS MANY
}
