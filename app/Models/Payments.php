<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model 
{
    protected $table = "payments";
    public $timestamps = false;
    protected $fillable = [
        'transaction_id',
        'mop_id',
        'amount'
    ];


}