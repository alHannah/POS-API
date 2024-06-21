<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AreaAssignment extends Model
{
    protected $table = 'area_assignments';
    protected $fillable = [
        'area_id',
        'user_id'
    ];

    public function area_to_user(): BelongsTo
    {
        return $this->belongsTo(Users::class, 'user_id');
    }

    public function area_assignment_per_area () : BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
}
