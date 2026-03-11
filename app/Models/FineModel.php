<?php

namespace App\Models;

use CodeIgniter\Model;

class FineModel extends Model
{
    protected $table = 'fines';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'loan_id',
        'fine_type',
        'fine_label',
        'fine_per_day',
        'rate_amount',
        'rate_unit',
        'grace_days',
        'late_days',
        'quantity',
        'fulfillment_method',
        'amount',
        'paid_amount',
        'status',
        'calculated_at',
        'paid_at',
        'resolved_at',
        'notes',
    ];
}
