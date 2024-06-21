<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZReadingDetails extends Model
{
    protected $table = "z_reading_details";
    public $timestamps = false;
    protected $fillable = [
        'z_reading_id',
        'section_name',
        'description',
        'total_no',
        'amount'
    ];
}
