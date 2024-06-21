<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreAssignments extends Model
{
    protected $table = "store_assignments";
    protected $fillable = [
        'mobile_user_id',
        'store_id',
        'start_date',
        'end_date'
    ];

    public function assignment_per_store(): BelongsTo
    {
        return $this->BelongsTo(Store::class, 'store_id');
    }
}
