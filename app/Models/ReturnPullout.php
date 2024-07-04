<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnPullout extends Model
{
    protected $table = "return_pullouts";

    protected $fillable = [
        'user_id',
        'store_id',
        'ref_no',
        'date_submitted',
        'date_created',
        'status',
        'approved_by',
        'approved_date'
    ];
    // ---------------------------BELONGS TO

    public function returnPullout_stores () : BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    // ---------------------------HAS MANY
}
