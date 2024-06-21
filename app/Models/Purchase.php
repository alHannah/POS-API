<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = "purchase";
    protected $fillable = [
        'store_id',
        'submitted_by',
        'approved_by',
        'delivery_date',
        'type',
        'pr_no',
        'po_no',
        'date_created',
        'template_id',
        'is_modified',
        'remarks',
        'status'
    ];
}
