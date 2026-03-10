<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/index', [
            'pageTitle' => 'Dashboard',
            'activeMenu' => 'dashboard',
            'stats' => [
                ['label' => 'Total Buku', 'value' => '22'],
                ['label' => 'Total Anggota', 'value' => '8'],
                ['label' => 'Sedang Dipinjam', 'value' => '4'],
                ['label' => 'Terlambat', 'value' => '2'],
            ],
            'recentTransactions' => [
                ['book' => 'Mere Christianity', 'member' => 'Grace Okafor', 'date' => '2025-02-01', 'status' => 'overdue'],
                ['book' => 'Jesus Calling', 'member' => 'Samuel Adeyemi', 'date' => '2025-02-10', 'status' => 'borrowed'],
                ['book' => 'The Purpose Driven Life', 'member' => 'Grace Okafor', 'date' => '2025-02-12', 'status' => 'borrowed'],
                ['book' => 'Wild at Heart', 'member' => 'David Mensah', 'date' => '2025-01-20', 'status' => 'returned'],
                ['book' => 'Through Gates of Splendor', 'member' => 'Emmanuel Nwosu', 'date' => '2025-02-05', 'status' => 'overdue'],
            ],
            'lateSummaries' => [
                ['member' => 'Grace Okafor', 'book' => 'Mere Christianity', 'days_late' => 10, 'amount' => 'Rp 15.000'],
                ['member' => 'Emmanuel Nwosu', 'book' => 'Through Gates of Splendor', 'days_late' => 6, 'amount' => 'Rp 9.000'],
            ],
            'fineSummary' => [
                'unpaid' => 'Rp 24.000',
                'collected' => 'Rp 10.500',
            ],
        ]);
    }
}
