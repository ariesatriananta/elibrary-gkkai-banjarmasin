<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$errors = $errors ?? [];
$fineContext = $fineContext ?? [];
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Denda & Bonus</h1>
    <p class="page-description">Atur nominal denda, catat pembayaran, dan simpan catatan bonus peminjaman.</p>
  </div>
</div>

<div class="grid grid-cols-1 gap-6 xl:grid-cols-[0.8fr_1.2fr]">
  <form method="post" action="<?= site_url('fines/settings') ?>" class="panel-card p-6">
    <h2 class="section-heading mb-4">Pengaturan</h2>
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
    <div class="stat-card text-center">
      <p class="stat-card-label">Total Denda</p>
      <p class="mt-1 text-2xl font-bold"><?= esc(rupiah($summary['total'])) ?></p>
    </div>
    <div class="stat-card text-center">
      <p class="stat-card-label">Belum Lunas</p>
      <p class="mt-1 text-2xl font-bold text-destructive"><?= esc(rupiah($summary['unpaid'])) ?></p>
    </div>
    <div class="stat-card text-center">
      <p class="stat-card-label">Terkumpul</p>
      <p class="mt-1 text-2xl font-bold text-success"><?= esc(rupiah($summary['collected'])) ?></p>
    </div>
  </div>
</div>

<div class="space-y-4">
  <?php if ($fines === []): ?>
    <div class="empty-state">
      <h2 class="empty-state-title">Belum ada denda</h2>
      <p class="empty-state-description">Denda akan muncul otomatis saat ada pinjaman yang terlambat.</p>
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
              <div class="metric-tile">
                <p class="metric-tile-label">Hari Terlambat</p>
                <p class="metric-tile-value"><?= esc((string) $fine['late_days']) ?></p>
              </div>
              <div class="metric-tile">
                <p class="metric-tile-label">Tagihan</p>
                <p class="metric-tile-value"><?= esc(rupiah($fine['amount'])) ?></p>
              </div>
              <div class="metric-tile">
                <p class="metric-tile-label">Dibayar</p>
                <p class="metric-tile-value text-success"><?= esc(rupiah($fine['paid_amount'])) ?></p>
              </div>
              <div class="metric-tile">
                <p class="metric-tile-label">Sisa</p>
                <p class="metric-tile-value text-destructive"><?= esc(rupiah((float) $fine['amount'] - (float) $fine['paid_amount'])) ?></p>
              </div>
            </div>

            <div class="space-y-2 text-sm text-slate-500">
              <p>Jatuh tempo: <?= esc(format_indo_date($fine['due_at'])) ?></p>
              <p>Dihitung pada: <?= esc(format_indo_date($fine['calculated_at'], true)) ?></p>
            </div>
          </div>

          <div class="space-y-4">
            <?php if ($fine['status'] !== 'paid'): ?>
              <form method="post" action="<?= site_url('fines/' . $fine['id'] . '/pay') ?>" class="metric-tile">
                <h3 class="section-heading text-base">Pembayaran Denda</h3>
                <p class="section-description">Masukkan nominal yang dibayar sekarang.</p>
                <div class="mt-4 flex gap-3">
                  <input type="number" min="1" max="<?= esc((string) $remainingAmount) ?>" name="payment_amount" class="panel-input <?= $isPaymentContext ? field_error_class($errors, 'payment_amount') : '' ?>" value="<?= esc((string) ($isPaymentContext ? old('payment_amount', (string) $remainingAmount) : $remainingAmount)) ?>">
                  <button type="submit" class="panel-button">Simpan</button>
                </div>
                <?php if ($isPaymentContext && field_error($errors, 'payment_amount')): ?>
                  <p class="field-error mt-2"><?= esc(field_error($errors, 'payment_amount')) ?></p>
                <?php endif; ?>
              </form>
            <?php endif; ?>

            <form method="post" action="<?= site_url('fines/loan/' . $fine['loan_id'] . '/bonus-note') ?>" class="metric-tile">
              <h3 class="section-heading text-base">Catatan Bonus</h3>
              <p class="section-description">Catatan manual untuk apresiasi atau penilaian khusus.</p>
              <textarea name="note" rows="3" class="panel-input mt-4 <?= $isNoteContext ? field_error_class($errors, 'note') : '' ?>" placeholder="Contoh: Mengembalikan buku dalam kondisi sangat baik."><?= esc($isNoteContext ? old('note') : '') ?></textarea>
              <?php if ($isNoteContext && field_error($errors, 'note')): ?>
                <p class="field-error mt-2"><?= esc(field_error($errors, 'note')) ?></p>
              <?php endif; ?>
              <button type="submit" class="panel-button mt-3">Tambah Catatan</button>
            </form>

            <?php $notes = $bonusNotes[$fine['loan_id']] ?? []; ?>
            <div class="metric-tile">
              <h3 class="section-heading text-base">Riwayat Catatan Bonus</h3>
              <?php if ($notes === []): ?>
                <p class="section-description">Belum ada catatan bonus.</p>
              <?php else: ?>
                <div class="mt-3 space-y-3">
                  <?php foreach ($notes as $note): ?>
                    <div class="metric-tile">
                      <p class="text-sm"><?= esc($note['note']) ?></p>
                      <p class="mt-1 text-xs text-slate-500"><?= esc(format_indo_date($note['created_at'], true)) ?></p>
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
