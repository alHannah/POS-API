<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $table = 'discounts';
    protected $fillable = [
        'name',
        'type',
        'amount',
        'with_tax',
        'status'
    ];
}
