<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    // ---------------------------BELONGS TO

    public function receiving_stores () : BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    // ---------------------------HAS MANY
}
