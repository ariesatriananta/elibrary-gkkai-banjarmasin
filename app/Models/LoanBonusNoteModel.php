<?php

namespace App\Models;

use CodeIgniter\Model;

class LoanBonusNoteModel extends Model
{
    protected $table = 'loan_bonus_notes';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'loan_id',
        'created_by_admin_id',
        'note',
    ];
}
