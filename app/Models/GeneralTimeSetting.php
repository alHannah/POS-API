<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralTimeSetting extends Model
{
    protected $table = 'general_time_setting';
    protected $fillable = [
        'id',
        'start_time',
        'end_time'
    ];
}
