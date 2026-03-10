<?php

namespace App\Controllers;

class FineController extends BaseController
{
    public function index(): string
    {
        return view('fines/index', [
            'pageTitle' => 'Denda & Bonus',
            'activeMenu' => 'fines',
            'summary' => [
                'total' => 'Rp 34.500',
                'unpaid' => 'Rp 24.000',
                'collected' => 'Rp 10.500',
            ],
            'fines' => [
                ['member' => 'Grace Okafor', 'book' => 'Mere Christianity', 'days_late' => 10, 'amount' => 'Rp 15.000', 'date' => '2025-02-15', 'status' => 'unpaid'],
                ['member' => 'Emmanuel Nwosu', 'book' => 'Through Gates of Splendor', 'days_late' => 6, 'amount' => 'Rp 9.000', 'date' => '2025-02-19', 'status' => 'unpaid'],
                ['member' => 'Emmanuel Nwosu', 'book' => 'Knowing God', 'days_late' => 7, 'amount' => 'Rp 10.500', 'date' => '2025-01-29', 'status' => 'paid'],
            ],
        ]);
    }
}
