<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XReadingDetail extends Model
{
    protected $table = "x_reading_details";
    protected $fillable = [
        'x_reading_id',
        'section_name',
        'description',
        'total_no',
        'amount'
    ];
}
