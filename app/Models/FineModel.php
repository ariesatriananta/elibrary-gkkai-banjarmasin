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
        'fine_per_day',
        'late_days',
        'amount',
        'paid_amount',
        'status',
        'calculated_at',
        'paid_at',
        'notes',
    ];
}
