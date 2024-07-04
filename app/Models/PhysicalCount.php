<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhysicalCount extends Model
{
    protected $table = "physical_count";
    protected $fillable = [
        'user_id',
        'store_id',
        'inventory_no',
        'date_submitted',
        'remarks',
        'approved_id',
        'created_by',
        'date_approved',
        'date_created',
        'status',
        'mi_tag'
    ];
    // ---------------------------BELONGS TO

    public function physicalCount_stores () : BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    // ---------------------------HAS MANY

}
