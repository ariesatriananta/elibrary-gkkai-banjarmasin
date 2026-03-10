<?php

namespace App\Models;

use CodeIgniter\Model;

class BookCopyModel extends Model
{
    protected $table = 'book_copies';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'book_id',
        'copy_code',
        'legacy_code',
        'barcode_value',
        'status',
        'notes',
    ];
}
