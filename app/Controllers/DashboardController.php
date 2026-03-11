<?php

namespace App\Controllers;

use App\Models\BookModel;
use App\Models\BookCopyModel;
use App\Models\FineModel;
use App\Models\LoanModel;
use App\Models\MemberModel;

class DashboardController extends BaseController
{
    public function index(): string
    {
        service('libraryService')->syncStatuses();

        $bookModel = new BookModel();
        $bookCopyModel = new BookCopyModel();
        $memberModel = new MemberModel();
        $loanModel = new LoanModel();
        $fineModel = new FineModel();

        $recentTransactions = db_connect()->query("
            SELECT
                l.status,
                l.borrowed_at,
                b.title AS book_title,
                m.full_name AS member_name
            FROM loans l
            INNER JOIN book_copies bc ON bc.id = l.book_copy_id
            INNER JOIN books b ON b.id = bc.book_id
            INNER JOIN members m ON m.id = l.member_id
            ORDER BY l.created_at DESC, l.id DESC
            LIMIT 5
        ")->getResultArray();

        $lateSummaries = db_connect()->query("
            SELECT
                m.full_name AS member_name,
                b.title AS book_title,
                f.late_days,
                f.amount
            FROM fines f
            INNER JOIN loans l ON l.id = f.loan_id
            INNER JOIN members m ON m.id = l.member_id
            INNER JOIN book_copies bc ON bc.id = l.book_copy_id
            INNER JOIN books b ON b.id = bc.book_id
            WHERE f.fine_type = 'late'
              AND f.status IN ('unpaid', 'partial')
            ORDER BY f.amount DESC, f.id DESC
            LIMIT 5
        ")->getResultArray();

        $totalFine = (float) $fineModel->selectSum('amount')->first()['amount'];
        $paidFine = (float) $fineModel->selectSum('paid_amount')->first()['paid_amount'];

        return view('dashboard/index', [
            'pageTitle' => 'Dashboard',
            'activeMenu' => 'dashboard',
            'stats' => [
                ['label' => 'Total Buku', 'value' => (string) $bookCopyModel->countAllResults()],
                ['label' => 'Total Judul Buku', 'value' => (string) $bookModel->countAllResults()],
                ['label' => 'Total Anggota', 'value' => (string) $memberModel->countAllResults()],
                ['label' => 'Sedang Dipinjam', 'value' => (string) $loanModel->whereIn('status', ['borrowed', 'overdue'])->countAllResults()],
                ['label' => 'Terlambat', 'value' => (string) $loanModel->where('status', 'overdue')->countAllResults()],
            ],
            'recentTransactions' => $recentTransactions,
            'lateSummaries' => $lateSummaries,
            'fineSummary' => [
                'unpaid' => max(0, $totalFine - $paidFine),
                'collected' => $paidFine,
            ],
        ]);
    }
}
