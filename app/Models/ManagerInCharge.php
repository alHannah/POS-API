<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManagerInCharge extends Model
{
    protected $table = 'manager_in_charges';
    protected $fillable = [
        'store_id',
        'user_id'
    ];

    public function manager_user () : BelongsTo
    {
        return $this->belongsTo(Users::class,'user_id', 'id');
    }
}
