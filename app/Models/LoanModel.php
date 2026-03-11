<?php

namespace App\Models;

use CodeIgniter\Model;

class LoanModel extends Model
{
    protected $table = 'loans';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'member_id',
        'book_copy_id',
        'processed_by_admin_id',
        'borrowed_at',
        'due_at',
        'returned_at',
        'return_condition',
        'status',
        'notes',
    ];
}
