<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function schedule_groups_per_store () : HasMany {
        return $this->HasMany(StorePerSchedules::class, 'schedule_id');
    }
}
