<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashReports extends Model
{
    protected $table = 'cash_reports';
    protected $fillable = [
        'submitted_by',
        'store_id',
        'date_submitted',
        'type',
        'amount',
        'remarks'
    ];
}
