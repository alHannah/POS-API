<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillOfMaterial extends Model
{
    protected $table = 'bill_of_materials';
    protected $fillable = [
        'product_id',
        'bom_id',
        'qty',
        'uom_id'
    ];

    public function bom_per_uom(): BelongsTo {
        return $this->belongsTo(Uom::class, 'uom_id');
    }

    public function bom_per_product(): BelongsTo {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function bom_per_bomId(): BelongsTo {
        return $this->belongsTo(Product::class, 'bom_id');
    }

}
