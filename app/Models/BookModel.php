<?php

namespace App\Models;

use CodeIgniter\Model;

class BookModel extends Model
{
    protected $table = 'books';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'title',
        'author',
        'publisher',
        'publication_year',
        'isbn',
        'page_count',
        'category_id',
        'age_classification_id',
        'cover_path',
        'synopsis',
        'shelf_location',
        'legacy_status',
        'status',
    ];
}
