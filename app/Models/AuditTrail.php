<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    protected $table = 'audit_trail';

    protected $fillable = [
        'transaction_id',
        'brand_id',
        'reference_number',
        'store_id',
        'mobile_user_id',
        'user_id',
        'user_type',
        'number',
        'remarks'
    ];
}
