<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$errors = $errors ?? [];
$fineContext = $fineContext ?? [];
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Denda & Bonus</h1>
    <p class="page-description">Kelola aturan keterlambatan mingguan, denda kerusakan, dan kewajiban penggantian buku hilang.</p>
  </div>
</div>

<div class="grid grid-cols-1 gap-6 xl:grid-cols-[0.95fr_1.05fr]">
  <form method="post" action="<?= site_url('fines/settings') ?>" class="panel-card p-6">
    <h2 class="section-heading mb-4">Aturan Denda</h2>
    <div class="grid grid-cols-1 gap-4">
      <div>
        <label for="late_fine_per_week" class="mb-1 block text-sm font-medium">Denda Keterlambatan per Minggu</label>
        <input id="late_fine_per_week" name="late_fine_per_week" type="number" min="0" value="<?= esc(old('late_fine_per_week', (string) $settings['late_fine_per_week'])) ?>" class="panel-input <?= ($fineContext['panel'] ?? null) === 'settings' ? field_error_class($errors, 'late_fine_per_week') : '' ?>" required>
        <?php if (($fineContext['panel'] ?? null) === 'settings' && field_error($errors, 'late_fine_per_week')): ?>
          <p class="field-error mt-1"><?= esc(field_error($errors, 'late_fine_per_week')) ?></p>
        <?php endif; ?>
      </div>
      <div>
        <label for="late_grace_days" class="mb-1 block text-sm font-medium">Masa Tenggang Setelah Jatuh Tempo (hari)</label>
        <input id="late_grace_days" name="late_grace_days" type="number" min="0" value="<?= esc(old('late_grace_days', (string) $settings['late_grace_days'])) ?>" class="panel-input <?= ($fineContext['panel'] ?? null) === 'settings' ? field_error_class($errors, 'late_grace_days') : '' ?>" required>
        <?php if (($fineContext['panel'] ?? null) === 'settings' && field_error($errors, 'late_grace_days')): ?>
          <p class="field-error mt-1"><?= esc(field_error($errors, 'late_grace_days')) ?></p>
        <?php endif; ?>
      </div>
      <div>
        <label for="damage_fine_amount" class="mb-1 block text-sm font-medium">Denda Kerusakan per Buku</label>
        <input id="damage_fine_amount" name="damage_fine_amount" type="number" min="0" value="<?= esc(old('damage_fine_amount', (string) $settings['damage_fine_amount'])) ?>" class="panel-input <?= ($fineContext['panel'] ?? null) === 'settings' ? field_error_class($errors, 'damage_fine_amount') : '' ?>" required>
        <?php if (($fineContext['panel'] ?? null) === 'settings' && field_error($errors, 'damage_fine_amount')): ?>
          <p class="field-error mt-1"><?= esc(field_error($errors, 'damage_fine_amount')) ?></p>
        <?php endif; ?>
      </div>
      <div>
        <label for="loan_duration_days" class="mb-1 block text-sm font-medium">Durasi Pinjam Default (hari)</label>
        <input id="loan_duration_days" name="loan_duration_days" type="number" min="1" value="<?= esc(old('loan_duration_days', (string) $settings['loan_duration_days'])) ?>" class="panel-input <?= ($fineContext['panel'] ?? null) === 'settings' ? field_error_class($errors, 'loan_duration_days') : '' ?>" required>
        <?php if (($fineContext['panel'] ?? null) === 'settings' && field_error($errors, 'loan_duration_days')): ?>
          <p class="field-error mt-1"><?= esc(field_error($errors, 'loan_duration_days')) ?></p>
        <?php endif; ?>
      </div>
      <div class="soft-info">
        Denda keterlambatan dihitung per minggu dan baru aktif setelah melewati masa tenggang. Untuk buku hilang, anggota wajib mengganti buku dan tidak dikenakan nominal otomatis.
      </div>
      <button type="submit" class="panel-button w-fit">Simpan Aturan</button>
    </div>
  </form>

  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
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
    <div class="stat-card text-center">
      <p class="stat-card-label">Penggantian Buku</p>
      <p class="mt-1 text-2xl font-bold text-warning"><?= esc((string) $summary['open_replacements']) ?></p>
    </div>
  </div>
</div>

<div class="space-y-4">
  <?php if ($fines === []): ?>
    <div class="empty-state">
      <h2 class="empty-state-title">Belum ada kasus denda</h2>
      <p class="empty-state-description">Denda akan muncul otomatis dari keterlambatan, kerusakan, atau laporan kehilangan buku.</p>
    </div>
  <?php else: ?>
    <?php foreach ($fines as $fine): ?>
      <?php
      $isPaymentContext = ($fineContext['panel'] ?? null) === 'payment' && (int) ($fineContext['fine_id'] ?? 0) === (int) $fine['id'];
      $isResolveContext = ($fineContext['panel'] ?? null) === 'resolve' && (int) ($fineContext['fine_id'] ?? 0) === (int) $fine['id'];
      $isNoteContext = ($fineContext['panel'] ?? null) === 'note' && (int) ($fineContext['loan_id'] ?? 0) === (int) $fine['loan_id'];
      $remainingAmount = max(0, (int) ceil((float) $fine['amount'] - (float) $fine['paid_amount']));
      $notes = $bonusNotes[$fine['loan_id']] ?? [];
      $statusClass = match ($fine['status']) {
          'paid', 'resolved' => 'status-badge-returned',
          'partial' => 'status-badge-partial',
          'open', 'unpaid' => $fine['fine_type'] === 'lost' ? 'status-badge-neutral' : 'status-badge-overdue',
          default => 'status-badge-neutral',
      };
      ?>
      <div class="panel-card p-6">
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.05fr_0.95fr]">
          <div class="space-y-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
              <div>
                <div class="flex flex-wrap items-center gap-2">
                  <h2 class="text-lg font-semibold"><?= esc($fine['member_name']) ?></h2>
                  <span class="surface-chip surface-chip-primary"><?= esc(fine_type_label($fine['fine_type'])) ?></span>
                </div>
                <p class="text-sm text-slate-500"><?= esc($fine['book_title']) ?> - <?= esc($fine['copy_code']) ?></p>
              </div>
              <span class="status-badge <?= esc($statusClass) ?>">
                <?= esc(fine_status_label($fine['status'])) ?>
              </span>
            </div>

            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
              <div class="metric-tile">
                <p class="metric-tile-label">Jenis</p>
                <p class="metric-tile-value"><?= esc(fine_type_label($fine['fine_type'])) ?></p>
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
                <p class="metric-tile-label"><?= $fine['fine_type'] === 'lost' ? 'Status Ganti Buku' : 'Sisa' ?></p>
                <p class="metric-tile-value <?= $fine['fine_type'] === 'lost' ? 'text-warning' : 'text-destructive' ?>">
                  <?= $fine['fine_type'] === 'lost' ? esc($fine['status'] === 'resolved' ? 'Selesai' : 'Menunggu') : esc(rupiah((float) $fine['amount'] - (float) $fine['paid_amount'])) ?>
                </p>
              </div>
            </div>

            <div class="space-y-2 text-sm text-slate-500">
              <p>Jatuh tempo pinjam: <?= esc(format_indo_date($fine['due_at'])) ?></p>
              <?php if ($fine['fine_type'] === 'late'): ?>
                <p>Aturan: <?= esc(rupiah($fine['rate_amount'])) ?>/minggu setelah masa tenggang <?= esc((string) $fine['grace_days']) ?> hari.</p>
                <p>Keterlambatan tercatat: <?= esc((string) $fine['late_days']) ?> hari, ditagihkan <?= esc((string) $fine['quantity']) ?> minggu.</p>
              <?php elseif ($fine['fine_type'] === 'damage'): ?>
                <p>Denda kerusakan flat sebesar <?= esc(rupiah($fine['rate_amount'])) ?> per buku.</p>
                <p>Kondisi pengembalian: <?= esc(loan_condition_label($fine['return_condition'] ?? 'damaged')) ?>.</p>
              <?php else: ?>
                <p>Kasus kehilangan buku diselesaikan dengan penerimaan buku pengganti, bukan nominal uang.</p>
                <p>Kondisi pengembalian: <?= esc(loan_condition_label($fine['return_condition'] ?? 'lost')) ?>.</p>
              <?php endif; ?>
              <p>Dibuat/dihitung pada: <?= esc(format_indo_date($fine['calculated_at'], true)) ?></p>
              <?php if (! empty($fine['resolved_at'])): ?>
                <p>Diselesaikan pada: <?= esc(format_indo_date($fine['resolved_at'], true)) ?></p>
              <?php endif; ?>
            </div>
          </div>

          <div class="space-y-4">
            <?php if (($fine['fulfillment_method'] ?? 'payment') === 'payment' && $fine['status'] !== 'paid'): ?>
              <form method="post" action="<?= site_url('fines/' . $fine['id'] . '/pay') ?>" class="metric-tile">
                <h3 class="section-heading text-base">Pembayaran Denda</h3>
                <p class="section-description">Masukkan nominal pembayaran untuk kasus ini.</p>
                <div class="mt-4 flex gap-3">
                  <input type="number" min="1" max="<?= esc((string) max(1, $remainingAmount)) ?>" name="payment_amount" class="panel-input <?= $isPaymentContext ? field_error_class($errors, 'payment_amount') : '' ?>" value="<?= esc((string) ($isPaymentContext ? old('payment_amount', (string) max(1, $remainingAmount)) : max(1, $remainingAmount))) ?>">
                  <button type="submit" class="panel-button">Simpan</button>
                </div>
                <?php if ($isPaymentContext && field_error($errors, 'payment_amount')): ?>
                  <p class="field-error mt-2"><?= esc(field_error($errors, 'payment_amount')) ?></p>
                <?php endif; ?>
              </form>
            <?php endif; ?>

            <?php if ($fine['fine_type'] === 'lost' && $fine['status'] !== 'resolved'): ?>
              <form method="post" action="<?= site_url('fines/' . $fine['id'] . '/resolve') ?>" class="metric-tile">
                <h3 class="section-heading text-base">Penyelesaian Penggantian</h3>
                <p class="section-description">Gunakan saat buku pengganti sudah diterima dan stok boleh aktif kembali.</p>
                <textarea name="resolution_note" rows="3" class="panel-input mt-4 <?= $isResolveContext ? field_error_class($errors, 'resolution_note') : '' ?>" placeholder="Contoh: Buku pengganti edisi layak baca sudah diterima."><?= esc($isResolveContext ? old('resolution_note') : '') ?></textarea>
                <button type="submit" class="panel-button mt-3">Tandai Selesai</button>
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
