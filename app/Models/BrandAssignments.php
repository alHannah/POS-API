<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrandAssignments extends Model
{
    protected $table = 'brand_assignments';

    protected $fillable = [
        'brand_id',
        'user_id',
    ];

    public function brand_to_user () : BelongsTo
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
}
