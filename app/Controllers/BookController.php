<?php

namespace App\Controllers;

class BookController extends BaseController
{
    public function index(): string
    {
        return view('books/index', [
            'pageTitle' => 'Data Buku',
            'activeMenu' => 'books',
            'books' => [
                ['title' => 'Mere Christianity', 'author' => 'C.S. Lewis', 'category' => 'Teologi', 'available' => '2/3 tersedia', 'coverClass' => 'from-primary/80 to-primary/40', 'allBorrowed' => false],
                ['title' => 'The Purpose Driven Life', 'author' => 'Rick Warren', 'category' => 'Praktika Kristen', 'available' => '1/2 tersedia', 'coverClass' => 'from-accent/80 to-accent/40', 'allBorrowed' => false],
                ['title' => 'Jesus Calling', 'author' => 'Sarah Young', 'category' => 'Alkitab', 'available' => '0/1 tersedia', 'coverClass' => 'from-info/80 to-info/40', 'allBorrowed' => true],
                ['title' => 'Knowing God', 'author' => 'J.I. Packer', 'category' => 'Teologi', 'available' => '2/2 tersedia', 'coverClass' => 'from-success/80 to-success/40', 'allBorrowed' => false],
                ['title' => 'Wild at Heart', 'author' => 'John Eldredge', 'category' => 'Praktika Kristen', 'available' => '1/1 tersedia', 'coverClass' => 'from-warning/80 to-warning/40', 'allBorrowed' => false],
                ['title' => 'The Lion, the Witch and the Wardrobe', 'author' => 'C.S. Lewis', 'category' => 'Kesusastraan Kristen', 'available' => '3/4 tersedia', 'coverClass' => 'from-destructive/60 to-destructive/30', 'allBorrowed' => false],
                ['title' => 'Systematic Theology', 'author' => 'Wayne Grudem', 'category' => 'Teologi', 'available' => '1/2 tersedia', 'coverClass' => 'from-primary/80 to-primary/40', 'allBorrowed' => false],
                ['title' => 'Through Gates of Splendor', 'author' => 'Elisabeth Elliot', 'category' => 'Misi', 'available' => '0/1 tersedia', 'coverClass' => 'from-accent/80 to-accent/40', 'allBorrowed' => true],
            ],
            'categories' => ['Semua Kategori', 'Alkitab', 'Teologi', 'Sejarah Gereja', 'Praktika Kristen', 'Kesusastraan Kristen', 'Misi'],
        ]);
    }
}
