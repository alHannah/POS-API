<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UomCategory extends Model
{
    protected $table = "uom_categories";
    protected $fillable = [
        'name'
    ];
    // ---------------------------BELONGS TO

    public function uomCategory_stores () : BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
    // ---------------------------HAS MANY
    public function categories_per_uom(): HasMany {
        return $this->hasMany(Uom::class, 'uom_category_id');
    }
}
