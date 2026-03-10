<?php

namespace App\Controllers;

use App\Models\BookCopyModel;
use App\Models\LoanModel;
use App\Models\MemberModel;
use CodeIgniter\HTTP\RedirectResponse;

class TransactionController extends BaseController
{
    private MemberModel $memberModel;
    private BookCopyModel $copyModel;
    private LoanModel $loanModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->copyModel = new BookCopyModel();
        $this->loanModel = new LoanModel();
    }

    public function index(): string
    {
        service('libraryService')->syncStatuses();

        $defaultLoanDays = service('libraryService')->getSettingNumber('loan_duration_days', 14);
        $filters = [
            'q' => trim((string) $this->request->getGet('q')),
            'status' => trim((string) $this->request->getGet('status')),
        ];

        $history = $this->fetchHistory();
        $history = array_values(array_filter($history, function (array $row) use ($filters): bool {
            if ($filters['status'] !== '' && $row['status'] !== $filters['status']) {
                return false;
            }

            if ($filters['q'] === '') {
                return true;
            }

            $haystack = mb_strtolower(implode(' ', [
                $row['book_title'],
                $row['copy_code'],
                $row['member_name'],
                $row['member_number'],
            ]));

            return str_contains($haystack, mb_strtolower($filters['q']));
        }));

        $activeTab = session()->getFlashdata('transaction_tab');

        if (! in_array($activeTab, ['borrow', 'return', 'history'], true)) {
            $activeTab = 'history';
        }

        return view('transactions/index', [
            'pageTitle' => 'Peminjaman & Pengembalian',
            'activeMenu' => 'transactions',
            'members' => $this->memberModel->where('is_active', 1)->orderBy('full_name', 'ASC')->findAll(),
            'availableCopies' => $this->availableCopies(),
            'activeLoans' => $this->activeLoans(),
            'history' => $history,
            'filters' => $filters,
            'defaultBorrowDate' => date('Y-m-d'),
            'defaultDueDate' => date('Y-m-d', strtotime('+' . $defaultLoanDays . ' days')),
            'errors' => session('errors') ?? [],
            'activeTab' => $activeTab,
        ]);
    }

    public function borrow(): RedirectResponse
    {
        $rules = [
            'member_id' => 'required|integer',
            'book_copy_id' => 'required|integer',
            'borrowed_at' => 'required|valid_date[Y-m-d]',
            'due_at' => 'required|valid_date[Y-m-d]',
            'notes' => 'permit_empty|max_length[500]',
        ];

        if (! $this->validate($rules)) {
            return $this->redirectTransactionBack('borrow', 'Data peminjaman belum valid.', $this->validator->getErrors());
        }

        $memberId = (int) $this->request->getPost('member_id');
        $copyId = (int) $this->request->getPost('book_copy_id');
        $borrowedAt = (string) $this->request->getPost('borrowed_at');
        $dueAt = (string) $this->request->getPost('due_at');
        $member = $this->memberModel->find($memberId);
        $copy = $this->copyModel->find($copyId);

        if (! $member || (int) $member['is_active'] !== 1) {
            return $this->redirectTransactionBack('borrow', 'Anggota tidak valid atau nonaktif.', [
                'member_id' => 'Anggota tidak valid atau sudah nonaktif.',
            ]);
        }

        if (! $copy || $copy['status'] !== 'available') {
            return $this->redirectTransactionBack('borrow', 'Copy buku tidak tersedia untuk dipinjam.', [
                'book_copy_id' => 'Copy buku yang dipilih sudah tidak tersedia.',
            ]);
        }

        if (strtotime($dueAt) < strtotime($borrowedAt)) {
            return $this->redirectTransactionBack('borrow', 'Tanggal jatuh tempo tidak boleh sebelum tanggal pinjam.', [
                'due_at' => 'Tanggal jatuh tempo harus sama atau setelah tanggal pinjam.',
            ]);
        }

        service('libraryService')->borrowBookCopy(
            $memberId,
            $copyId,
            $borrowedAt,
            $dueAt,
            session('admin_id') ? (int) session('admin_id') : null,
            trim((string) $this->request->getPost('notes'))
        );

        return redirect()->to(site_url('transactions'))->with('transaction_tab', 'borrow')->with('success', 'Peminjaman berhasil dicatat.');
    }

    public function return(): RedirectResponse
    {
        $rules = [
            'loan_id' => 'required|integer',
            'returned_at' => 'required|valid_date[Y-m-d]',
            'notes' => 'permit_empty|max_length[500]',
        ];

        if (! $this->validate($rules)) {
            return $this->redirectTransactionBack('return', 'Data pengembalian belum valid.', $this->validator->getErrors());
        }

        $loanId = (int) $this->request->getPost('loan_id');
        $loan = $this->loanModel->find($loanId);
        $returnedAt = (string) $this->request->getPost('returned_at');

        if (! $loan || ! in_array($loan['status'], ['borrowed', 'overdue'], true)) {
            return $this->redirectTransactionBack('return', 'Pinjaman aktif tidak ditemukan.', [
                'loan_id' => 'Pinjaman aktif yang dipilih tidak ditemukan.',
            ]);
        }

        if (strtotime($returnedAt) < strtotime(substr((string) $loan['borrowed_at'], 0, 10))) {
            return $this->redirectTransactionBack('return', 'Tanggal kembali tidak boleh sebelum tanggal pinjam.', [
                'returned_at' => 'Tanggal kembali harus sama atau setelah tanggal pinjam.',
            ]);
        }

        service('libraryService')->returnLoan(
            $loanId,
            $returnedAt,
            trim((string) $this->request->getPost('notes'))
        );

        return redirect()->to(site_url('transactions'))->with('transaction_tab', 'return')->with('success', 'Pengembalian berhasil dicatat.');
    }

    private function redirectTransactionBack(string $tab, string $message, array $errors = []): RedirectResponse
    {
        return redirect()
            ->back()
            ->withInput()
            ->with('transaction_tab', $tab)
            ->with('errors', $errors)
            ->with('error', $message);
    }

    private function availableCopies(): array
    {
        $sql = "
            SELECT
                bc.id,
                bc.copy_code,
                bc.barcode_value,
                bc.legacy_code,
                b.title,
                b.author
            FROM book_copies bc
            INNER JOIN books b ON b.id = bc.book_id
            WHERE bc.status = 'available'
            ORDER BY b.title ASC, bc.copy_code ASC
        ";

        return db_connect()->query($sql)->getResultArray();
    }

    private function activeLoans(): array
    {
        $sql = "
            SELECT
                l.id,
                l.borrowed_at,
                l.due_at,
                l.status,
                b.title AS book_title,
                bc.copy_code,
                m.full_name AS member_name,
                m.member_number
            FROM loans l
            INNER JOIN book_copies bc ON bc.id = l.book_copy_id
            INNER JOIN books b ON b.id = bc.book_id
            INNER JOIN members m ON m.id = l.member_id
            WHERE l.status IN ('borrowed', 'overdue')
            ORDER BY l.due_at ASC, l.id ASC
        ";

        return db_connect()->query($sql)->getResultArray();
    }

    private function fetchHistory(): array
    {
        $sql = "
            SELECT
                l.id,
                l.borrowed_at,
                l.due_at,
                l.returned_at,
                l.status,
                l.notes,
                b.title AS book_title,
                bc.copy_code,
                m.full_name AS member_name,
                m.member_number,
                f.amount AS fine_amount,
                f.paid_amount AS fine_paid_amount,
                f.status AS fine_status
            FROM loans l
            INNER JOIN book_copies bc ON bc.id = l.book_copy_id
            INNER JOIN books b ON b.id = bc.book_id
            INNER JOIN members m ON m.id = l.member_id
            LEFT JOIN fines f ON f.loan_id = l.id
            ORDER BY l.borrowed_at DESC, l.id DESC
        ";

        return db_connect()->query($sql)->getResultArray();
    }
}
