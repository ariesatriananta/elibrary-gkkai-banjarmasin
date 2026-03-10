<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberModel extends Model
{
    protected $table = 'members';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'member_number',
        'full_name',
        'phone',
        'email',
        'address',
        'notes',
        'is_active',
        'joined_at',
    ];
}
