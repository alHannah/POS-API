<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModeOfPayment extends Model
{
    protected $table = "mode_of_payments";
    protected $fillable = [
        'name',
        'code',
        'status'
    ];

    public function mop_price_tier () : HasMany
    {
        return $this->hasMany(PriceTier::class, 'mop_id');
    }

}
