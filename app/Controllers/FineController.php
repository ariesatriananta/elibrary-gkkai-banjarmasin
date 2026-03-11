<?php

namespace App\Controllers;

use App\Models\FineModel;
use App\Models\LoanBonusNoteModel;
use App\Models\SettingModel;
use CodeIgniter\HTTP\RedirectResponse;

class FineController extends BaseController
{
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

        $fines = $this->fineRows();
        $loanIds = array_column($fines, 'loan_id');
        $bonusNotes = $loanIds === [] ? [] : $this->bonusNotesByLoan($loanIds);

        return view('fines/index', [
            'pageTitle' => 'Denda & Bonus',
            'activeMenu' => 'fines',
            'summary' => $this->summary($fines),
            'fines' => $fines,
            'bonusNotes' => $bonusNotes,
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

        return redirect()->to(site_url('fines'))->with('success', 'Pengaturan denda berhasil diperbarui.');
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

        return redirect()->to(site_url('fines'))->with('success', 'Pembayaran denda berhasil dicatat.');
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

        return redirect()->to(site_url('fines'))->with('success', 'Kasus kehilangan buku telah ditandai selesai.');
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

        return redirect()->to(site_url('fines'))->with('success', 'Catatan bonus berhasil ditambahkan.');
    }

    private function fineRows(): array
    {
        $sql = "
            SELECT
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
            FROM fines f
            INNER JOIN loans l ON l.id = f.loan_id
            INNER JOIN book_copies bc ON bc.id = l.book_copy_id
            INNER JOIN books b ON b.id = bc.book_id
            INNER JOIN members m ON m.id = l.member_id
            ORDER BY
                CASE
                    WHEN f.status IN ('unpaid', 'partial', 'open') THEN 1
                    WHEN f.status = 'resolved' THEN 2
                    ELSE 3
                END,
                f.calculated_at DESC,
                f.id DESC
        ";

        return db_connect()->query($sql)->getResultArray();
    }

    private function summary(array $fines): array
    {
        $paymentFines = array_values(array_filter($fines, fn (array $fine): bool => ($fine['fulfillment_method'] ?? 'payment') === 'payment'));
        $total = array_sum(array_map(fn (array $fine): float => (float) $fine['amount'], $paymentFines));
        $collected = array_sum(array_map(fn (array $fine): float => (float) $fine['paid_amount'], $paymentFines));
        $openReplacements = count(array_filter($fines, fn (array $fine): bool => ($fine['fine_type'] ?? '') === 'lost' && ($fine['status'] ?? '') === 'open'));

        return [
            'total' => $total,
            'unpaid' => max(0, $total - $collected),
            'collected' => $collected,
            'open_replacements' => $openReplacements,
        ];
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
}
