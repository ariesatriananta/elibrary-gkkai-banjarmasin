<?php

namespace App\Controllers;

use App\Models\BookCopyModel;
use App\Models\LoanModel;
use App\Models\MemberModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TransactionController extends BaseController
{
    private const HISTORY_PER_PAGE = 15;

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
        $filters = $this->historyFilters();
        $historyPagination = $this->historyPaginationState($filters);
        $history = $this->fetchHistory($filters, $historyPagination['per_page'], $historyPagination['page']);

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
            'historyPagination' => $historyPagination,
            'defaultBorrowDate' => date('Y-m-d'),
            'defaultDueDate' => date('Y-m-d', strtotime('+' . $defaultLoanDays . ' days')),
            'errors' => session('errors') ?? [],
            'activeTab' => $activeTab,
            'fineRules' => [
                'late_fine_per_week' => service('libraryService')->getSettingNumber('late_fine_per_week', 5000),
                'late_grace_days' => service('libraryService')->getSettingNumber('late_grace_days', 3),
                'damage_fine_amount' => service('libraryService')->getSettingNumber('damage_fine_amount', 100000),
            ],
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

    public function export(): ResponseInterface
    {
        service('libraryService')->syncStatuses();

        $filters = $this->historyFilters();
        $history = $this->fetchHistoryForExport($filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Riwayat Transaksi');

        $headers = [
            'No',
            'Tanggal Pinjam',
            'Tanggal Jatuh Tempo',
            'Tanggal Kembali',
            'Anggota',
            'Nomor Anggota',
            'Judul Buku',
            'Kode Copy',
            'Status',
            'Kondisi Kembali',
            'Total Denda',
            'Terbayar',
            'Sisa Denda',
            'Catatan',
        ];

        $sheet->fromArray($headers, null, 'A1');
        foreach ($headers as $index => $header) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->getStyle('A1:N1')->getFont()->setBold(true);

        $rowNumber = 2;

        foreach ($history as $index => $row) {
            $fineAmount = (float) ($row['fine_amount'] ?? 0);
            $finePaidAmount = (float) ($row['fine_paid_amount'] ?? 0);
            $openReplacementCount = (int) ($row['open_replacement_count'] ?? 0);

            $sheet->fromArray([
                $index + 1,
                $row['borrowed_at'] ? format_indo_date($row['borrowed_at']) : '-',
                $row['due_at'] ? format_indo_date($row['due_at']) : '-',
                $row['returned_at'] ? format_indo_date($row['returned_at']) : '-',
                $row['member_name'] ?: '-',
                $row['member_number'] ?: '-',
                $row['book_title'] ?: '-',
                $row['copy_code'] ?: '-',
                loan_status_label((string) ($row['status'] ?? '')),
                $row['return_condition'] ? loan_condition_label((string) $row['return_condition']) : '-',
                $openReplacementCount > 0 ? 'Menunggu Penggantian' : ($fineAmount > 0 ? rupiah($fineAmount) : '-'),
                $openReplacementCount > 0 ? '-' : ($finePaidAmount > 0 ? rupiah($finePaidAmount) : '-'),
                $openReplacementCount > 0 ? '-' : (($fineAmount - $finePaidAmount) > 0 ? rupiah($fineAmount - $finePaidAmount) : '-'),
                $row['notes'] ?: '-',
            ], null, 'A' . $rowNumber);

            $rowNumber++;
        }

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'transactions-export-');
        $writer->save($tempFile);

        $fileName = 'riwayat-transaksi-' . date('Y-m-d-His') . '.xlsx';
        $content = file_get_contents($tempFile) ?: '';
        @unlink($tempFile);

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setHeader('Cache-Control', 'max-age=0')
            ->setBody($content);
    }

    public function return(): RedirectResponse
    {
        $rules = [
            'loan_id' => 'required|integer',
            'returned_at' => 'required|valid_date[Y-m-d]',
            'return_condition' => 'required|in_list[good,damaged,lost]',
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
            (string) $this->request->getPost('return_condition'),
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

    private function historyFilters(): array
    {
        $status = trim((string) $this->request->getGet('status'));

        return [
            'q' => trim((string) $this->request->getGet('q')),
            'status' => in_array($status, ['borrowed', 'overdue', 'returned', 'lost'], true) ? $status : '',
        ];
    }

    private function historyPaginationState(array $filters): array
    {
        $summaryRow = $this->historyBaseBuilder($filters)
            ->select('COUNT(*) AS total_rows', false)
            ->get()
            ->getRowArray() ?? [];

        $totalRows = (int) ($summaryRow['total_rows'] ?? 0);
        $perPage = self::HISTORY_PER_PAGE;
        $totalPages = max(1, (int) ceil($totalRows / $perPage));
        $page = max(1, (int) $this->request->getGet('history_page'));
        $page = min($page, $totalPages);
        $from = $totalRows > 0 ? (($page - 1) * $perPage) + 1 : 0;
        $to = $totalRows > 0 ? min($from + $perPage - 1, $totalRows) : 0;

        return [
            'page' => $page,
            'per_page' => $perPage,
            'total_rows' => $totalRows,
            'total_pages' => $totalPages,
            'from' => $from,
            'to' => $to,
        ];
    }

    private function fetchHistory(array $filters, int $perPage, int $page): array
    {
        return $this->historyBaseBuilder($filters)
            ->select("
                l.id,
                l.borrowed_at,
                l.due_at,
                l.returned_at,
                l.return_condition,
                l.status,
                l.notes,
                b.title AS book_title,
                bc.copy_code,
                m.full_name AS member_name,
                m.member_number,
                (
                    SELECT COALESCE(SUM(f.amount), 0)
                    FROM fines f
                    WHERE f.loan_id = l.id
                      AND f.fulfillment_method = 'payment'
                ) AS fine_amount,
                (
                    SELECT COALESCE(SUM(f.paid_amount), 0)
                    FROM fines f
                    WHERE f.loan_id = l.id
                      AND f.fulfillment_method = 'payment'
                ) AS fine_paid_amount,
                (
                    SELECT COUNT(*)
                    FROM fines f
                    WHERE f.loan_id = l.id
                      AND f.fine_type = 'lost'
                      AND f.status = 'open'
                ) AS open_replacement_count
            ", false)
            ->orderBy('l.borrowed_at', 'DESC')
            ->orderBy('l.id', 'DESC')
            ->limit($perPage, max(0, ($page - 1) * $perPage))
            ->get()
            ->getResultArray();
    }

    private function fetchHistoryForExport(array $filters): array
    {
        return $this->historyBaseBuilder($filters)
            ->select("
                l.id,
                l.borrowed_at,
                l.due_at,
                l.returned_at,
                l.return_condition,
                l.status,
                l.notes,
                b.title AS book_title,
                bc.copy_code,
                m.full_name AS member_name,
                m.member_number,
                (
                    SELECT COALESCE(SUM(f.amount), 0)
                    FROM fines f
                    WHERE f.loan_id = l.id
                      AND f.fulfillment_method = 'payment'
                ) AS fine_amount,
                (
                    SELECT COALESCE(SUM(f.paid_amount), 0)
                    FROM fines f
                    WHERE f.loan_id = l.id
                      AND f.fulfillment_method = 'payment'
                ) AS fine_paid_amount,
                (
                    SELECT COUNT(*)
                    FROM fines f
                    WHERE f.loan_id = l.id
                      AND f.fine_type = 'lost'
                      AND f.status = 'open'
                ) AS open_replacement_count
            ", false)
            ->orderBy('l.borrowed_at', 'DESC')
            ->orderBy('l.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    private function historyBaseBuilder(array $filters)
    {
        $builder = db_connect()->table('loans l');

        $builder
            ->join('book_copies bc', 'bc.id = l.book_copy_id')
            ->join('books b', 'b.id = bc.book_id')
            ->join('members m', 'm.id = l.member_id');

        if ($filters['status'] !== '') {
            $builder->where('l.status', $filters['status']);
        }

        if ($filters['q'] !== '') {
            $builder
                ->groupStart()
                ->like('b.title', $filters['q'])
                ->orLike('bc.copy_code', $filters['q'])
                ->orLike('m.full_name', $filters['q'])
                ->orLike('m.member_number', $filters['q'])
                ->groupEnd();
        }

        return $builder;
    }
}
