<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportDetail extends Model
{
    protected $table = "report_details";

    protected $fillable = [
        'sales_report_id',
        'section_name',
        'description',
        'total_no',
        'amount'
    ];
}
