<?php

namespace App\Services;

use App\Models\BookCopyModel;
use App\Models\BookModel;
use App\Models\FineModel;
use App\Models\LoanBonusNoteModel;
use App\Models\LoanModel;
use App\Models\MemberModel;
use App\Models\SettingModel;

class LibraryService
{
    private SettingModel $settingModel;
    private BookModel $bookModel;
    private BookCopyModel $copyModel;
    private MemberModel $memberModel;
    private LoanModel $loanModel;
    private FineModel $fineModel;
    private LoanBonusNoteModel $bonusNoteModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
        $this->bookModel = new BookModel();
        $this->copyModel = new BookCopyModel();
        $this->memberModel = new MemberModel();
        $this->loanModel = new LoanModel();
        $this->fineModel = new FineModel();
        $this->bonusNoteModel = new LoanBonusNoteModel();
    }

    public function syncStatuses(): void
    {
        $now = date('Y-m-d H:i:s');

        $activeLoans = $this->loanModel
            ->whereIn('status', ['borrowed', 'overdue'])
            ->findAll();

        foreach ($activeLoans as $loan) {
            $isOverdue = strtotime($loan['due_at']) < time();
            $targetStatus = $isOverdue ? 'overdue' : 'borrowed';

            if ($loan['status'] !== $targetStatus) {
                $this->loanModel->update($loan['id'], ['status' => $targetStatus]);
            }

            $loan['status'] = $targetStatus;
            $this->syncLateFineForLoan($loan, $now);
        }

        foreach ($this->copyModel->findAll() as $copy) {
            $hasLostCase = db_connect()->query("
                SELECT COUNT(*) AS aggregate
                FROM loans l
                INNER JOIN fines f ON f.loan_id = l.id
                WHERE l.book_copy_id = ?
                  AND f.fine_type = 'lost'
                  AND f.status = 'open'
            ", [$copy['id']])->getRowArray();

            $hasActiveLoan = $this->loanModel
                ->where('book_copy_id', $copy['id'])
                ->whereIn('status', ['borrowed', 'overdue'])
                ->countAllResults() > 0;

            $targetStatus = ((int) ($hasLostCase['aggregate'] ?? 0) > 0) ? 'lost' : ($hasActiveLoan ? 'borrowed' : 'available');

            if ($copy['status'] !== $targetStatus) {
                $this->copyModel->update($copy['id'], ['status' => $targetStatus]);
            }
        }

        foreach ($this->bookModel->findAll() as $book) {
            $this->refreshBookStatus((int) $book['id']);
        }
    }

    public function nextMemberNumber(): string
    {
        $last = $this->memberModel->orderBy('id', 'DESC')->first();
        $next = $last ? ((int) preg_replace('/\D/', '', (string) $last['member_number'])) + 1 : 1;

        return 'AGT-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    public function getSettingNumber(string $key, int $default): int
    {
        $setting = $this->settingModel->where('setting_key', $key)->first();

        if (! $setting || ! is_numeric((string) $setting['setting_value'])) {
            return $default;
        }

        return (int) $setting['setting_value'];
    }

    public function saveSettings(int $finePerDay, int $loanDurationDays): void
    {
        $this->upsertSetting('late_fine_per_week', (string) $finePerDay, 'number', 'Denda Keterlambatan per Minggu');
        $this->upsertSetting('loan_duration_days', (string) $loanDurationDays, 'number', 'Durasi Pinjam Default');
    }

    public function saveFineRules(int $lateFinePerWeek, int $lateGraceDays, int $damageFineAmount, int $loanDurationDays): void
    {
        $this->upsertSetting('late_fine_per_week', (string) $lateFinePerWeek, 'number', 'Denda Keterlambatan per Minggu');
        $this->upsertSetting('late_grace_days', (string) $lateGraceDays, 'number', 'Masa Tenggang Keterlambatan (hari)');
        $this->upsertSetting('damage_fine_amount', (string) $damageFineAmount, 'number', 'Denda Kerusakan Buku');
        $this->upsertSetting('loan_duration_days', (string) $loanDurationDays, 'number', 'Durasi Pinjam Default');
    }

    public function borrowBookCopy(int $memberId, int $copyId, string $borrowedAt, string $dueAt, ?int $adminId, ?string $notes = null): void
    {
        $this->loanModel->insert([
            'member_id' => $memberId,
            'book_copy_id' => $copyId,
            'processed_by_admin_id' => $adminId,
            'borrowed_at' => $borrowedAt . ' 00:00:00',
            'due_at' => $dueAt . ' 23:59:59',
            'status' => 'borrowed',
            'notes' => $notes,
        ]);

        $this->copyModel->update($copyId, ['status' => 'borrowed']);
        $copy = $this->copyModel->find($copyId);
        if ($copy) {
            $this->refreshBookStatus((int) $copy['book_id']);
        }
    }

    public function returnLoan(int $loanId, string $returnedAt, string $condition = 'good', ?string $notes = null): void
    {
        $loan = $this->loanModel->find($loanId);

        if (! $loan) {
            return;
        }

        $returnedAtDateTime = $returnedAt . ' 23:59:59';
        $loanStatus = $condition === 'lost' ? 'lost' : 'returned';

        $this->loanModel->update($loanId, [
            'returned_at' => $returnedAtDateTime,
            'return_condition' => $condition,
            'status' => $loanStatus,
            'notes' => $notes ?: $loan['notes'],
        ]);

        $this->copyModel->update($loan['book_copy_id'], [
            'status' => $condition === 'lost' ? 'lost' : 'available',
        ]);

        if ($condition !== 'lost') {
            $this->syncLateFineForLoan([
                ...$loan,
                'returned_at' => $returnedAtDateTime,
                'status' => $loanStatus,
            ], date('Y-m-d H:i:s'), $returnedAtDateTime);
        } else {
            $this->removeFineIfUnpaid($loanId, 'late');
        }

        if ($condition === 'damaged') {
            $damageFineAmount = $this->getSettingNumber('damage_fine_amount', 100000);
            $this->upsertFine($loanId, 'damage', [
                'fine_label' => 'Denda Kerusakan Buku',
                'fine_per_day' => 0,
                'rate_amount' => $damageFineAmount,
                'rate_unit' => 'book',
                'grace_days' => 0,
                'late_days' => 0,
                'quantity' => 1,
                'amount' => $damageFineAmount,
                'calculated_at' => date('Y-m-d H:i:s'),
                'fulfillment_method' => 'payment',
            ]);
        } else {
            $this->removeFineIfUnpaid($loanId, 'damage');
        }

        if ($condition === 'lost') {
            $this->upsertFine($loanId, 'lost', [
                'fine_label' => 'Penggantian Buku Hilang',
                'fine_per_day' => 0,
                'rate_amount' => 0,
                'rate_unit' => 'replacement',
                'grace_days' => 0,
                'late_days' => 0,
                'quantity' => 1,
                'amount' => 0,
                'calculated_at' => date('Y-m-d H:i:s'),
                'paid_amount' => 0,
                'status' => 'open',
                'fulfillment_method' => 'replacement',
                'resolved_at' => null,
            ]);
        } else {
            $this->removeFineIfUnpaid($loanId, 'lost');
        }

        $copy = $this->copyModel->find($loan['book_copy_id']);
        if ($copy) {
            $this->refreshBookStatus((int) $copy['book_id']);
        }
    }

    public function payFine(int $fineId, float $paymentAmount): void
    {
        $fine = $this->fineModel->find($fineId);

        if (! $fine) {
            return;
        }

        $newPaidAmount = min((float) $fine['amount'], (float) $fine['paid_amount'] + $paymentAmount);
        $status = $newPaidAmount >= (float) $fine['amount']
            ? 'paid'
            : ($newPaidAmount > 0 ? 'partial' : 'unpaid');

        $this->fineModel->update($fineId, [
            'paid_amount' => $newPaidAmount,
            'status' => $status,
            'paid_at' => $status === 'paid' ? date('Y-m-d H:i:s') : $fine['paid_at'],
            'resolved_at' => $status === 'paid' ? date('Y-m-d H:i:s') : $fine['resolved_at'],
        ]);
    }

    public function resolveReplacementFine(int $fineId, ?int $adminId, ?string $note = null): void
    {
        $fine = $this->fineModel->find($fineId);

        if (! $fine || $fine['fine_type'] !== 'lost') {
            return;
        }

        $resolvedAt = date('Y-m-d H:i:s');

        $this->fineModel->update($fineId, [
            'status' => 'resolved',
            'resolved_at' => $resolvedAt,
            'notes' => $note !== null && trim($note) !== '' ? trim($note) : $fine['notes'],
        ]);

        $loan = $this->loanModel->find($fine['loan_id']);

        if (! $loan) {
            return;
        }

        $copy = $this->copyModel->find($loan['book_copy_id']);

        if (! $copy) {
            return;
        }

        $copyNotes = trim((string) ($copy['notes'] ?? ''));
        $copyNotes = trim($copyNotes . "\nPenggantian buku diterima pada {$resolvedAt}.");

        $this->copyModel->update($loan['book_copy_id'], [
            'status' => 'available',
            'notes' => $copyNotes,
        ]);

        $this->addBonusNote($loan['id'], $adminId, $note !== null && trim($note) !== '' ? trim($note) : 'Penggantian buku hilang telah diterima.');
        $this->refreshBookStatus((int) $copy['book_id']);
    }

    public function addBonusNote(int $loanId, ?int $adminId, string $note): void
    {
        $this->bonusNoteModel->insert([
            'loan_id' => $loanId,
            'created_by_admin_id' => $adminId,
            'note' => trim($note),
        ]);
    }

    public function refreshBookStatus(int $bookId): void
    {
        $availableCount = $this->copyModel
            ->where('book_id', $bookId)
            ->where('status', 'available')
            ->countAllResults();

        $totalCount = $this->copyModel
            ->where('book_id', $bookId)
            ->countAllResults();

        $status = $totalCount === 0 ? 'available' : ($availableCount > 0 ? 'available' : 'borrowed');
        $this->bookModel->update($bookId, ['status' => $status]);
    }

    private function syncLateFineForLoan(array $loan, string $calculatedAt, ?string $comparisonDateTime = null): void
    {
        $comparisonTimestamp = strtotime($comparisonDateTime ?? date('Y-m-d H:i:s'));
        $dueTimestamp = strtotime((string) $loan['due_at']);
        $lateDays = $comparisonTimestamp > $dueTimestamp
            ? max(1, (int) floor(($comparisonTimestamp - $dueTimestamp) / 86400))
            : 0;

        $graceDays = $this->getSettingNumber('late_grace_days', 3);
        $effectiveLateDays = max(0, $lateDays - $graceDays);

        if ($effectiveLateDays <= 0) {
            $this->removeFineIfUnpaid((int) $loan['id'], 'late');
            return;
        }

        $lateFinePerWeek = $this->getSettingNumber('late_fine_per_week', 5000);
        $lateWeeks = (int) ceil($effectiveLateDays / 7);
        $amount = $lateWeeks * $lateFinePerWeek;

        $this->upsertFine((int) $loan['id'], 'late', [
            'fine_label' => 'Denda Keterlambatan',
            'fine_per_day' => 0,
            'rate_amount' => $lateFinePerWeek,
            'rate_unit' => 'week',
            'grace_days' => $graceDays,
            'late_days' => $lateDays,
            'quantity' => $lateWeeks,
            'amount' => $amount,
            'calculated_at' => $calculatedAt,
            'fulfillment_method' => 'payment',
        ]);
    }

    private function upsertFine(int $loanId, string $type, array $payload): void
    {
        $existingFine = $this->fineModel
            ->where('loan_id', $loanId)
            ->where('fine_type', $type)
            ->first();

        $basePayload = [
            'loan_id' => $loanId,
            'fine_type' => $type,
            'paid_amount' => $existingFine['paid_amount'] ?? 0,
            'status' => $payload['status'] ?? $this->resolvePaymentStatus((float) ($existingFine['paid_amount'] ?? 0), (float) ($payload['amount'] ?? 0), $payload['fulfillment_method'] ?? 'payment', $existingFine['status'] ?? null),
            'resolved_at' => $payload['resolved_at'] ?? (($existingFine['status'] ?? null) === 'paid' ? ($existingFine['resolved_at'] ?? date('Y-m-d H:i:s')) : null),
        ];

        $payload = array_merge($basePayload, $payload);

        if (($payload['fulfillment_method'] ?? 'payment') === 'payment') {
            $payload['status'] = $this->resolvePaymentStatus(
                (float) ($payload['paid_amount'] ?? 0),
                (float) ($payload['amount'] ?? 0),
                'payment',
                $existingFine['status'] ?? null
            );
            $payload['resolved_at'] = $payload['status'] === 'paid'
                ? ($existingFine['resolved_at'] ?? date('Y-m-d H:i:s'))
                : null;
        }

        if ($existingFine) {
            $this->fineModel->update($existingFine['id'], $payload);
            return;
        }

        $this->fineModel->insert($payload);
    }

    private function removeFineIfUnpaid(int $loanId, string $type): void
    {
        $fine = $this->fineModel
            ->where('loan_id', $loanId)
            ->where('fine_type', $type)
            ->first();

        if (! $fine) {
            return;
        }

        if ((float) $fine['paid_amount'] > 0 || in_array($fine['status'], ['partial', 'paid', 'resolved'], true)) {
            return;
        }

        $this->fineModel->delete($fine['id']);
    }

    private function resolvePaymentStatus(float $paidAmount, float $amount, string $fulfillmentMethod, ?string $existingStatus = null): string
    {
        if ($fulfillmentMethod !== 'payment') {
            return $existingStatus ?? 'open';
        }

        if ($amount <= 0) {
            return 'paid';
        }

        if ($paidAmount >= $amount) {
            return 'paid';
        }

        return $paidAmount > 0 ? 'partial' : 'unpaid';
    }

    private function upsertSetting(string $key, string $value, string $type, string $label): void
    {
        $existing = $this->settingModel->where('setting_key', $key)->first();

        $payload = [
            'setting_key' => $key,
            'setting_value' => $value,
            'value_type' => $type,
            'label' => $label,
        ];

        if ($existing) {
            $this->settingModel->update($existing['id'], $payload);
            return;
        }

        $this->settingModel->insert($payload);
    }
}
