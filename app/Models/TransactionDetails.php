<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetails extends Model
{
    protected $table = "transaction_details";
    public $timestamps = false;
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
