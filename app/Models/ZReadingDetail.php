<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZReadingDetail extends Model
{
    protected $table = "z_reading_details";
    protected $fillable = [
        'z_reading_id',
        'section_name',
        'description',
        'total_no',
        'amount'
    ];
}
