<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XReadingDetails extends Model
{
    protected $table = "x_reading_details";
    public $timestamps = false;
    protected $fillable = [
        'x_reading_id',
        'section_name',
        'description',
        'total_no',
        'amount'
    ];
}
