<?php

namespace App\Models;

use CodeIgniter\Model;

class AgeClassificationModel extends Model
{
    protected $table = 'age_classifications';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'name',
        'slug',
        'description',
        'sort_order',
    ];
}
