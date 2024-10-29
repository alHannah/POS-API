<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnPulloutDetail extends Model
{
    protected $table = "return_pullouts_details";

    protected $fillable = [
        'return_id',
        'product_id',
        'uom_id',
        'qty',
        'status',
        'remarks'
    ];

    public function details_returnPullouts () : BelongsTo
    {
        return $this->belongsTo(ReturnPullout::class, 'return_id');
    }

    public function details_products () : BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function details_uom () : BelongsTo
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }
}
