<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YReadingDetails extends Model
{
    protected $table = "y_reading_details";
    public $timestamps = false;
    protected $fillable = [
        'y_reading_id',
        'section_name',
        'description',
        'total_no',
        'amount'
    ];
}
