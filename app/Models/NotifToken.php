<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotifToken extends Model
{
    protected $table = "notif_tokens";
    protected $fillable = [
        'token',
        'mobile_user_id',
        'store_id'
    ];
    // ---------------------------BELONGS TO

    public function notifToken_stores () : BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    // ---------------------------HAS MANY

}
