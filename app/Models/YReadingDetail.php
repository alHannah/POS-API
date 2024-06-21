<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YReadingDetail extends Model
{
    protected $table = "y_reading_details";
    protected $fillable = [
        'y_reading_id',
        'section_name',
        'description',
        'total_no',
        'amount'
    ];
}
