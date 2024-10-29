<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function returnPullout_users () : BelongsTo
    {
        return $this->belongsTo(Users::class, 'approved_by');
    }

    public function returnPullout_mobileUsers () : BelongsTo
    {
        return $this->belongsTo(MobileUser::class, 'user_id');
    }

    // ---------------------------HAS MANY

    public function returnPullout_details () : HasMany
    {
        return $this->hasMany(ReturnPulloutDetail::class, 'return_id');
    }
}
