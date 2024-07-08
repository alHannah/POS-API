<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackagingDetail extends Model
{
    protected $table = "packaging_details";
    protected $fillable = [
        'packaging_id',
        'product_id',
        'qty',
        'uom_id'
    ];

    public function detail_per_packaging () : BelongsTo
    {
        return $this->belongsTo(Packaging::class, 'packaging_id');
    }

}
