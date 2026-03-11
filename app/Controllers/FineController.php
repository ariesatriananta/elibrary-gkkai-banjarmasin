<?php

namespace App\Controllers;

use App\Models\FineModel;
use App\Models\LoanBonusNoteModel;
use App\Models\SettingModel;
use CodeIgniter\HTTP\RedirectResponse;

class FineController extends BaseController
{
    private const PER_PAGE = 8;

    private FineModel $fineModel;
    private LoanBonusNoteModel $bonusNoteModel;
    private SettingModel $settingModel;

    public function __construct()
    {
        $this->fineModel = new FineModel();
        $this->bonusNoteModel = new LoanBonusNoteModel();
        $this->settingModel = new SettingModel();
    }

    public function index(): string
    {
        service('libraryService')->syncStatuses();

        $filters = $this->filters();
        $pagination = $this->paginationState($filters);
        $fines = $this->fineRows($filters, $pagination['per_page'], $pagination['page']);
        $loanIds = array_column($fines, 'loan_id');
        $bonusNotes = $loanIds === [] ? [] : $this->bonusNotesByLoan($loanIds);

        return view('fines/index', [
            'pageTitle' => 'Denda & Bonus',
            'activeMenu' => 'fines',
            'summary' => $pagination['summary'],
            'fines' => $fines,
            'bonusNotes' => $bonusNotes,
            'filters' => $filters,
            'pagination' => $pagination,
            'errors' => session('errors') ?? [],
            'fineContext' => session()->getFlashdata('fine_context') ?? [],
            'settings' => [
                'late_fine_per_week' => service('libraryService')->getSettingNumber('late_fine_per_week', 5000),
                'late_grace_days' => service('libraryService')->getSettingNumber('late_grace_days', 3),
                'damage_fine_amount' => service('libraryService')->getSettingNumber('damage_fine_amount', 100000),
                'loan_duration_days' => service('libraryService')->getSettingNumber('loan_duration_days', 14),
            ],
        ]);
    }

    public function updateSettings(): RedirectResponse
    {
        $rules = [
            'late_fine_per_week' => 'required|integer|greater_than_equal_to[0]',
            'late_grace_days' => 'required|integer|greater_than_equal_to[0]',
            'damage_fine_amount' => 'required|integer|greater_than_equal_to[0]',
            'loan_duration_days' => 'required|integer|greater_than_equal_to[1]',
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('fine_context', ['panel' => 'settings'])
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Pengaturan denda belum valid.');
        }

        service('libraryService')->saveFineRules(
            (int) $this->request->getPost('late_fine_per_week'),
            (int) $this->request->getPost('late_grace_days'),
            (int) $this->request->getPost('damage_fine_amount'),
            (int) $this->request->getPost('loan_duration_days')
        );

        service('libraryService')->syncStatuses();

        return $this->redirectToFines()->with('success', 'Pengaturan denda berhasil diperbarui.');
    }

    public function pay(int $fineId): RedirectResponse
    {
        $fine = $this->fineModel->find($fineId);

        if (! $fine) {
            return redirect()->to(site_url('fines'))->with('error', 'Data denda tidak ditemukan.');
        }

        if (($fine['fulfillment_method'] ?? 'payment') !== 'payment') {
            return redirect()->to(site_url('fines'))->with('error', 'Jenis kasus ini tidak dibayar dengan nominal uang.');
        }

        $paymentInput = trim((string) $this->request->getPost('payment_amount'));
        $paymentAmount = (float) $paymentInput;
        $remainingAmount = max(0, (float) $fine['amount'] - (float) $fine['paid_amount']);

        if ($paymentInput === '' || ! is_numeric($paymentInput) || $paymentAmount <= 0) {
            return redirect()
                ->back()
                ->withInput()
                ->with('fine_context', ['panel' => 'payment', 'fine_id' => $fineId])
                ->with('errors', ['payment_amount' => 'Nominal pembayaran harus lebih dari nol.'])
                ->with('error', 'Nominal pembayaran belum valid.');
        }

        if ($paymentAmount > $remainingAmount) {
            return redirect()
                ->back()
                ->withInput()
                ->with('fine_context', ['panel' => 'payment', 'fine_id' => $fineId])
                ->with('errors', ['payment_amount' => 'Nominal melebihi sisa tagihan denda.'])
                ->with('error', 'Nominal pembayaran melebihi sisa tagihan.');
        }

        service('libraryService')->payFine($fineId, $paymentAmount);

        return $this->redirectToFines()->with('success', 'Pembayaran denda berhasil dicatat.');
    }

    public function resolve(int $fineId): RedirectResponse
    {
        $fine = $this->fineModel->find($fineId);

        if (! $fine) {
            return redirect()->to(site_url('fines'))->with('error', 'Data denda tidak ditemukan.');
        }

        if (($fine['fine_type'] ?? '') !== 'lost') {
            return redirect()->to(site_url('fines'))->with('error', 'Hanya kasus kehilangan yang dapat diselesaikan dengan penggantian buku.');
        }

        $note = trim((string) $this->request->getPost('resolution_note'));

        service('libraryService')->resolveReplacementFine(
            $fineId,
            session('admin_id') ? (int) session('admin_id') : null,
            $note !== '' ? $note : null
        );

        return $this->redirectToFines()->with('success', 'Kasus kehilangan buku telah ditandai selesai.');
    }

    public function addBonusNote(int $loanId): RedirectResponse
    {
        $note = trim((string) $this->request->getPost('note'));

        if ($note === '') {
            return redirect()
                ->back()
                ->withInput()
                ->with('fine_context', ['panel' => 'note', 'loan_id' => $loanId])
                ->with('errors', ['note' => 'Catatan bonus tidak boleh kosong.'])
                ->with('error', 'Catatan bonus tidak boleh kosong.');
        }

        service('libraryService')->addBonusNote($loanId, session('admin_id') ? (int) session('admin_id') : null, $note);

        return $this->redirectToFines()->with('success', 'Catatan bonus berhasil ditambahkan.');
    }

    private function filters(): array
    {
        $type = trim((string) $this->request->getGet('type'));
        $status = trim((string) $this->request->getGet('status'));

        return [
            'q' => trim((string) $this->request->getGet('q')),
            'type' => in_array($type, ['late', 'damage', 'lost'], true) ? $type : '',
            'status' => in_array($status, ['active', 'unpaid', 'partial', 'paid', 'open', 'resolved'], true) ? $status : '',
        ];
    }

    private function paginationState(array $filters): array
    {
        $summaryRow = $this->fineBaseBuilder($filters)
            ->select("
                COUNT(*) AS total_rows,
                COALESCE(SUM(CASE WHEN COALESCE(f.fulfillment_method, 'payment') = 'payment' THEN f.amount ELSE 0 END), 0) AS total_amount,
                COALESCE(SUM(CASE WHEN COALESCE(f.fulfillment_method, 'payment') = 'payment' THEN f.paid_amount ELSE 0 END), 0) AS collected_amount,
                COALESCE(SUM(CASE WHEN f.fine_type = 'lost' AND f.status = 'open' THEN 1 ELSE 0 END), 0) AS open_replacements
            ", false)
            ->get()
            ->getRowArray() ?? [];

        $totalRows = (int) ($summaryRow['total_rows'] ?? 0);
        $perPage = self::PER_PAGE;
        $totalPages = max(1, (int) ceil($totalRows / $perPage));
        $page = max(1, (int) $this->request->getGet('page'));
        $page = min($page, $totalPages);
        $offset = $totalRows > 0 ? (($page - 1) * $perPage) + 1 : 0;
        $through = $totalRows > 0 ? min($offset + $perPage - 1, $totalRows) : 0;

        return [
            'page' => $page,
            'per_page' => $perPage,
            'total_rows' => $totalRows,
            'total_pages' => $totalPages,
            'from' => $offset,
            'to' => $through,
            'summary' => [
                'total' => (float) ($summaryRow['total_amount'] ?? 0),
                'unpaid' => max(0, (float) ($summaryRow['total_amount'] ?? 0) - (float) ($summaryRow['collected_amount'] ?? 0)),
                'collected' => (float) ($summaryRow['collected_amount'] ?? 0),
                'open_replacements' => (int) ($summaryRow['open_replacements'] ?? 0),
            ],
        ];
    }

    private function fineRows(array $filters, int $perPage, int $page): array
    {
        return $this->fineBaseBuilder($filters)
            ->select("
                f.id,
                f.loan_id,
                f.fine_type,
                f.fine_label,
                f.rate_amount,
                f.rate_unit,
                f.grace_days,
                f.quantity,
                f.fulfillment_method,
                f.fine_per_day,
                f.late_days,
                f.amount,
                f.paid_amount,
                f.status,
                f.calculated_at,
                f.paid_at,
                f.resolved_at,
                f.notes,
                l.due_at,
                l.return_condition,
                b.title AS book_title,
                bc.copy_code,
                m.full_name AS member_name
            ", false)
            ->orderBy("
                CASE
                    WHEN f.status IN ('unpaid', 'partial', 'open') THEN 1
                    WHEN f.status = 'resolved' THEN 2
                    ELSE 3
                END
            ", '', false)
            ->orderBy('f.calculated_at', 'DESC')
            ->orderBy('f.id', 'DESC')
            ->limit($perPage, max(0, ($page - 1) * $perPage))
            ->get()
            ->getResultArray();
    }

    private function fineBaseBuilder(array $filters)
    {
        $builder = db_connect()->table('fines f');

        $builder
            ->join('loans l', 'l.id = f.loan_id')
            ->join('book_copies bc', 'bc.id = l.book_copy_id')
            ->join('books b', 'b.id = bc.book_id')
            ->join('members m', 'm.id = l.member_id');

        if ($filters['q'] !== '') {
            $builder
                ->groupStart()
                ->like('m.full_name', $filters['q'])
                ->orLike('b.title', $filters['q'])
                ->orLike('bc.copy_code', $filters['q'])
                ->groupEnd();
        }

        if ($filters['type'] !== '') {
            $builder->where('f.fine_type', $filters['type']);
        }

        match ($filters['status']) {
            'active' => $builder->whereIn('f.status', ['unpaid', 'partial', 'open']),
            'unpaid' => $builder->where('f.status', 'unpaid'),
            'partial' => $builder->where('f.status', 'partial'),
            'paid' => $builder->where('f.status', 'paid'),
            'open' => $builder->where('f.status', 'open'),
            'resolved' => $builder->where('f.status', 'resolved'),
            default => null,
        };

        return $builder;
    }

    private function bonusNotesByLoan(array $loanIds): array
    {
        $rows = $this->bonusNoteModel
            ->whereIn('loan_id', $loanIds)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $grouped = [];

        foreach ($rows as $row) {
            $grouped[$row['loan_id']][] = $row;
        }

        return $grouped;
    }

    private function redirectToFines(): RedirectResponse
    {
        return redirect()->back() ?? redirect()->to(site_url('fines'));
    }
}
