<?php

namespace App\Controllers;

class TransactionController extends BaseController
{
    public function index(): string
    {
        return view('transactions/index', [
            'pageTitle' => 'Peminjaman & Pengembalian',
            'activeMenu' => 'transactions',
            'members' => ['Grace Okafor', 'Samuel Adeyemi', 'Blessing Eze', 'David Mensah', 'Ruth Amadi', 'Emmanuel Nwosu'],
            'books' => ['Mere Christianity', 'The Purpose Driven Life', 'Knowing God', 'Wild at Heart', 'Systematic Theology', 'Through Gates of Splendor'],
            'activeLoans' => [
                'Mere Christianity - Grace Okafor',
                'Jesus Calling - Samuel Adeyemi',
                'The Purpose Driven Life - Grace Okafor',
                'Through Gates of Splendor - Emmanuel Nwosu',
            ],
            'history' => [
                ['book' => 'Mere Christianity', 'member' => 'Grace Okafor', 'borrowed' => '2025-02-01', 'due' => '2025-02-15', 'returned' => '-', 'status' => 'overdue'],
                ['book' => 'Jesus Calling', 'member' => 'Samuel Adeyemi', 'borrowed' => '2025-02-10', 'due' => '2025-02-24', 'returned' => '-', 'status' => 'borrowed'],
                ['book' => 'The Purpose Driven Life', 'member' => 'Grace Okafor', 'borrowed' => '2025-02-12', 'due' => '2025-02-26', 'returned' => '-', 'status' => 'borrowed'],
                ['book' => 'Wild at Heart', 'member' => 'David Mensah', 'borrowed' => '2025-01-20', 'due' => '2025-02-03', 'returned' => '2025-02-02', 'status' => 'returned'],
                ['book' => 'Through Gates of Splendor', 'member' => 'Emmanuel Nwosu', 'borrowed' => '2025-02-05', 'due' => '2025-02-19', 'returned' => '-', 'status' => 'overdue'],
            ],
        ]);
    }
}
