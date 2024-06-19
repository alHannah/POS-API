<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModeOfPayments extends Model 
{
    protected $table = "mode_of_payments";
    public $timestamps = false;
    protected $fillable = [
        'name',
        'code',
        'status'
    ];


}