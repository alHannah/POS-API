<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receiving extends Model
{
    protected $table = "receiving";
    protected $fillable = [
        'po_no',
        'store_id',
        'purchase_id',
        'submitted_by',
        'receiving_no',
        'approved_by',
        'approved_date',
        'approved_delivery_date',
        'sync_date',
        'received_by',
        'status'
    ];
}
