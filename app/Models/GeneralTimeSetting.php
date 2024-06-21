<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralTimeSetting extends Model
{
    protected $table = 'general_time_setting';
    protected $fillable = [
        'start_time',
        'end_time'
    ];
}
