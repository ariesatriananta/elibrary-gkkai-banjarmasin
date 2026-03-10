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
        $finePerDay = $this->getSettingNumber('fine_per_day', 1500);
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

            if ($isOverdue) {
                $lateDays = max(1, (int) floor((time() - strtotime($loan['due_at'])) / 86400));
                $amount = $lateDays * $finePerDay;
                $existingFine = $this->fineModel->where('loan_id', $loan['id'])->first();

                if ($existingFine) {
                    $status = ((float) $existingFine['paid_amount']) >= $amount
                        ? 'paid'
                        : (((float) $existingFine['paid_amount']) > 0 ? 'partial' : 'unpaid');

                    $this->fineModel->update($existingFine['id'], [
                        'fine_per_day' => $finePerDay,
                        'late_days' => $lateDays,
                        'amount' => $amount,
                        'status' => $status,
                        'calculated_at' => $now,
                    ]);
                } else {
                    $this->fineModel->insert([
                        'loan_id' => $loan['id'],
                        'fine_per_day' => $finePerDay,
                        'late_days' => $lateDays,
                        'amount' => $amount,
                        'paid_amount' => 0,
                        'status' => 'unpaid',
                        'calculated_at' => $now,
                    ]);
                }
            }
        }

        foreach ($this->copyModel->findAll() as $copy) {
            $hasActiveLoan = $this->loanModel
                ->where('book_copy_id', $copy['id'])
                ->whereIn('status', ['borrowed', 'overdue'])
                ->countAllResults() > 0;

            $targetStatus = $hasActiveLoan ? 'borrowed' : 'available';

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
        $this->upsertSetting('fine_per_day', (string) $finePerDay, 'number', 'Denda per Hari');
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

    public function returnLoan(int $loanId, string $returnedAt, ?string $notes = null): void
    {
        $loan = $this->loanModel->find($loanId);

        if (! $loan) {
            return;
        }

        $returnedAtDateTime = $returnedAt . ' 23:59:59';
        $isLate = strtotime($returnedAtDateTime) > strtotime($loan['due_at']);
        $lateDays = $isLate ? max(1, (int) floor((strtotime($returnedAtDateTime) - strtotime($loan['due_at'])) / 86400)) : 0;
        $finePerDay = $this->getSettingNumber('fine_per_day', 1500);
        $amount = $lateDays * $finePerDay;

        $this->loanModel->update($loanId, [
            'returned_at' => $returnedAtDateTime,
            'status' => 'returned',
            'notes' => $notes ?: $loan['notes'],
        ]);

        $this->copyModel->update($loan['book_copy_id'], ['status' => 'available']);

        if ($amount > 0) {
            $existingFine = $this->fineModel->where('loan_id', $loanId)->first();

            if ($existingFine) {
                $this->fineModel->update($existingFine['id'], [
                    'fine_per_day' => $finePerDay,
                    'late_days' => $lateDays,
                    'amount' => $amount,
                    'calculated_at' => date('Y-m-d H:i:s'),
                    'status' => ((float) $existingFine['paid_amount']) >= $amount ? 'paid' : (((float) $existingFine['paid_amount']) > 0 ? 'partial' : 'unpaid'),
                ]);
            } else {
                $this->fineModel->insert([
                    'loan_id' => $loanId,
                    'fine_per_day' => $finePerDay,
                    'late_days' => $lateDays,
                    'amount' => $amount,
                    'paid_amount' => 0,
                    'status' => 'unpaid',
                    'calculated_at' => date('Y-m-d H:i:s'),
                ]);
            }
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
        ]);
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
