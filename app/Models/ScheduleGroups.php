<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleGroups extends Model
{
    protected $table = "schedule_groups";

    protected $fillable = [
        'name',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday'
    ];
}
