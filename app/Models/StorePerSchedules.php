<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StorePerSchedules extends Model
{
    protected $table = "store_per_schedules";
    public $timestamps = false;

    protected $fillable = [
        'schedule_id',
        'store_id'
    ];

    public function schedule_per_stores () : BelongsTo {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function store_schedule_per_schedule_group () : BelongsTo {
        return $this->belongsTo(ScheduleGroups::class, 'schedule_id');
    }
}
