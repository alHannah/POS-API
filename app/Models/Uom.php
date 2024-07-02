<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Uom extends Model
{
    protected $table = "uom";
    protected $fillable = [
        'name',
        'uom_category_id',
        'quantity'
    ];

    public function uom_per_categories(): BelongsTo {
        return $this->belongsTo(UomCategory::class, 'uom_category_id');
    }

    public function uom_per_bom(): HasMany {
        return $this->hasMany(BillOfMaterial::class, 'uom_id');
    }
}
