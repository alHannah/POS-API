<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashReport extends Model
{
    protected $table = 'cash_reports';
    protected $fillable = [
        'submitted_by',
        'store_id',
        'date_submitted',
        'type',
        'amount',
        'remarks'
    ];
    // ---------------------------BELONGS TO

    public function cashReport_stores () : BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    // ---------------------------HAS MANY
}
