<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $table = "transaction_details";
    protected $fillable = [
        'transaction_id',
        'product_id',
        'qty',
        'price',
        'total',
        'discount_id',
        'discount_amt',
        'order_type_id',
        'foc',
        'remarks'
    ];
}
