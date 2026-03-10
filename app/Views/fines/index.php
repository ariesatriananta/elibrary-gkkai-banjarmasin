<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$errors = $errors ?? [];
$fineContext = $fineContext ?? [];
?>
<div>
  <h1 class="text-2xl font-bold tracking-tight">Denda & Bonus</h1>
  <p class="text-slate-500">Atur nominal denda, catat pembayaran, dan simpan catatan bonus peminjaman.</p>
</div>

<div class="grid grid-cols-1 gap-6 xl:grid-cols-[0.8fr_1.2fr]">
  <form method="post" action="<?= site_url('fines/settings') ?>" class="panel-card p-6">
    <h2 class="mb-4 text-lg font-semibold">Pengaturan</h2>
    <div class="grid grid-cols-1 gap-4">
      <div>
        <label for="fine_per_day" class="mb-1 block text-sm font-medium">Denda per Hari</label>
        <input id="fine_per_day" name="fine_per_day" type="number" min="0" value="<?= esc(old('fine_per_day', (string) $settings['fine_per_day'])) ?>" class="panel-input <?= ($fineContext['panel'] ?? null) === 'settings' ? field_error_class($errors, 'fine_per_day') : '' ?>" required>
        <?php if (($fineContext['panel'] ?? null) === 'settings' && field_error($errors, 'fine_per_day')): ?>
          <p class="field-error mt-1"><?= esc(field_error($errors, 'fine_per_day')) ?></p>
        <?php endif; ?>
      </div>
      <div>
        <label for="loan_duration_days" class="mb-1 block text-sm font-medium">Durasi Pinjam Default (hari)</label>
        <input id="loan_duration_days" name="loan_duration_days" type="number" min="1" value="<?= esc(old('loan_duration_days', (string) $settings['loan_duration_days'])) ?>" class="panel-input <?= ($fineContext['panel'] ?? null) === 'settings' ? field_error_class($errors, 'loan_duration_days') : '' ?>" required>
        <?php if (($fineContext['panel'] ?? null) === 'settings' && field_error($errors, 'loan_duration_days')): ?>
          <p class="field-error mt-1"><?= esc(field_error($errors, 'loan_duration_days')) ?></p>
        <?php endif; ?>
      </div>
      <button type="submit" class="panel-button w-fit">Simpan Pengaturan</button>
    </div>
  </form>

  <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div class="panel-card p-5 text-center">
      <p class="text-sm text-slate-500">Total Denda</p>
      <p class="mt-1 text-2xl font-bold"><?= esc(rupiah($summary['total'])) ?></p>
    </div>
    <div class="panel-card p-5 text-center">
      <p class="text-sm text-slate-500">Belum Lunas</p>
      <p class="mt-1 text-2xl font-bold text-destructive"><?= esc(rupiah($summary['unpaid'])) ?></p>
    </div>
    <div class="panel-card p-5 text-center">
      <p class="text-sm text-slate-500">Terkumpul</p>
      <p class="mt-1 text-2xl font-bold text-success"><?= esc(rupiah($summary['collected'])) ?></p>
    </div>
  </div>
</div>

<div class="space-y-4">
  <?php if ($fines === []): ?>
    <div class="panel-card p-8 text-center">
      <h2 class="text-lg font-semibold">Belum ada denda</h2>
      <p class="mt-2 text-sm text-slate-500">Denda akan muncul otomatis saat ada pinjaman yang terlambat.</p>
    </div>
  <?php else: ?>
    <?php foreach ($fines as $fine): ?>
      <?php
      $isPaymentContext = ($fineContext['panel'] ?? null) === 'payment' && (int) ($fineContext['fine_id'] ?? 0) === (int) $fine['id'];
      $isNoteContext = ($fineContext['panel'] ?? null) === 'note' && (int) ($fineContext['loan_id'] ?? 0) === (int) $fine['loan_id'];
      $remainingAmount = max(1, (int) ceil((float) $fine['amount'] - (float) $fine['paid_amount']));
      ?>
      <div class="panel-card p-6">
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.1fr_0.9fr]">
          <div class="space-y-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
              <div>
                <h2 class="text-lg font-semibold"><?= esc($fine['member_name']) ?></h2>
                <p class="text-sm text-slate-500"><?= esc($fine['book_title']) ?> - <?= esc($fine['copy_code']) ?></p>
              </div>
              <span class="status-badge <?= $fine['status'] === 'paid' ? 'status-badge-returned' : ($fine['status'] === 'partial' ? 'status-badge-partial' : 'status-badge-overdue') ?>">
                <?= esc(fine_status_label($fine['status'])) ?>
              </span>
            </div>

            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
              <div class="rounded-xl bg-muted p-4">
                <p class="text-xs uppercase tracking-wide text-slate-400">Hari Terlambat</p>
                <p class="mt-2 text-lg font-semibold"><?= esc((string) $fine['late_days']) ?></p>
              </div>
              <div class="rounded-xl bg-muted p-4">
                <p class="text-xs uppercase tracking-wide text-slate-400">Tagihan</p>
                <p class="mt-2 text-lg font-semibold"><?= esc(rupiah($fine['amount'])) ?></p>
              </div>
              <div class="rounded-xl bg-muted p-4">
                <p class="text-xs uppercase tracking-wide text-slate-400">Dibayar</p>
                <p class="mt-2 text-lg font-semibold text-success"><?= esc(rupiah($fine['paid_amount'])) ?></p>
              </div>
              <div class="rounded-xl bg-muted p-4">
                <p class="text-xs uppercase tracking-wide text-slate-400">Sisa</p>
                <p class="mt-2 text-lg font-semibold text-destructive"><?= esc(rupiah((float) $fine['amount'] - (float) $fine['paid_amount'])) ?></p>
              </div>
            </div>

            <div class="space-y-2 text-sm text-slate-500">
              <p>Jatuh tempo: <?= esc(substr((string) $fine['due_at'], 0, 10)) ?></p>
              <p>Dihitung pada: <?= esc(substr((string) $fine['calculated_at'], 0, 16)) ?></p>
            </div>
          </div>

          <div class="space-y-4">
            <?php if ($fine['status'] !== 'paid'): ?>
              <form method="post" action="<?= site_url('fines/' . $fine['id'] . '/pay') ?>" class="rounded-2xl border border-border p-4">
                <h3 class="text-base font-semibold">Pembayaran Denda</h3>
                <p class="mt-1 text-sm text-slate-500">Masukkan nominal yang dibayar sekarang.</p>
                <div class="mt-4 flex gap-3">
                  <input type="number" min="1" max="<?= esc((string) $remainingAmount) ?>" name="payment_amount" class="panel-input <?= $isPaymentContext ? field_error_class($errors, 'payment_amount') : '' ?>" value="<?= esc((string) ($isPaymentContext ? old('payment_amount', (string) $remainingAmount) : $remainingAmount)) ?>">
                  <button type="submit" class="panel-button">Simpan</button>
                </div>
                <?php if ($isPaymentContext && field_error($errors, 'payment_amount')): ?>
                  <p class="field-error mt-2"><?= esc(field_error($errors, 'payment_amount')) ?></p>
                <?php endif; ?>
              </form>
            <?php endif; ?>

            <form method="post" action="<?= site_url('fines/loan/' . $fine['loan_id'] . '/bonus-note') ?>" class="rounded-2xl border border-border p-4">
              <h3 class="text-base font-semibold">Catatan Bonus</h3>
              <p class="mt-1 text-sm text-slate-500">Catatan manual untuk apresiasi atau penilaian khusus.</p>
              <textarea name="note" rows="3" class="panel-input mt-4 <?= $isNoteContext ? field_error_class($errors, 'note') : '' ?>" placeholder="Contoh: Mengembalikan buku dalam kondisi sangat baik."><?= esc($isNoteContext ? old('note') : '') ?></textarea>
              <?php if ($isNoteContext && field_error($errors, 'note')): ?>
                <p class="field-error mt-2"><?= esc(field_error($errors, 'note')) ?></p>
              <?php endif; ?>
              <button type="submit" class="panel-button mt-3">Tambah Catatan</button>
            </form>

            <?php $notes = $bonusNotes[$fine['loan_id']] ?? []; ?>
            <div class="rounded-2xl border border-border p-4">
              <h3 class="text-base font-semibold">Riwayat Catatan Bonus</h3>
              <?php if ($notes === []): ?>
                <p class="mt-2 text-sm text-slate-500">Belum ada catatan bonus.</p>
              <?php else: ?>
                <div class="mt-3 space-y-3">
                  <?php foreach ($notes as $note): ?>
                    <div class="rounded-xl bg-muted p-3">
                      <p class="text-sm"><?= esc($note['note']) ?></p>
                      <p class="mt-1 text-xs text-slate-500"><?= esc(substr((string) $note['created_at'], 0, 16)) ?></p>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageSkeleton') ?>
<?= $this->include('partials/skeletons/fines') ?>
<?= $this->endSection() ?>
