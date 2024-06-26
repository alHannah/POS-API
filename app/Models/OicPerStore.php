<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OicPerStore extends Model
{
    protected $table = "oic_per_stores";
    protected $fillable = [
        'mobile_user_id',
        'store_id'
    ];

    public function oic_stores () : BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function oic_mobile_user () : BelongsTo
    {
        return $this->belongsTo(MobileUser::class, 'mobile_user_id');
    }
}
