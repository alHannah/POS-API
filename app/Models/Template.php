<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Template extends Model
{
    protected $table = "templates";
    protected $fillable = [
        'pr_template',
        'user_id',
        'status',
        'store_id'
    ];
    // ---------------------------BELONGS TO

    public function template_stores () : BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    // ---------------------------HAS MANY
}
