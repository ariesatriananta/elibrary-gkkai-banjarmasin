<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$errors = $errors ?? [];
$fineContext = $fineContext ?? [];
$settingsModalOpen = ($fineContext['panel'] ?? null) === 'settings';
$filters = $filters ?? ['q' => '', 'type' => '', 'status' => ''];
$pagination = $pagination ?? ['page' => 1, 'total_pages' => 1, 'total_rows' => 0, 'from' => 0, 'to' => 0];
$pageQueryBase = array_filter([
    'q' => $filters['q'] ?? '',
    'type' => $filters['type'] ?? '',
    'status' => $filters['status'] ?? '',
], static fn ($value): bool => $value !== '' && $value !== null);
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Denda & Bonus</h1>
    <p class="page-description">Kelola aturan keterlambatan mingguan, denda kerusakan, dan kewajiban penggantian buku hilang.</p>
  </div>
  <button type="button" id="fine-settings-open" class="panel-button">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
      <circle cx="12" cy="12" r="3"></circle>
      <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82L4.21 7.2a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.01A1.65 1.65 0 0 0 10 3.25V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.01a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.01A1.65 1.65 0 0 0 20.75 10H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
    </svg>
    Aturan Denda
  </button>
</div>

<div class="grid auto-rows-min content-start items-start grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
  <div class="stat-card h-auto text-center">
    <p class="stat-card-label">Total Denda</p>
    <p class="mt-1 text-2xl font-bold"><?= esc(rupiah($summary['total'])) ?></p>
  </div>
  <div class="stat-card h-auto text-center">
    <p class="stat-card-label">Belum Lunas</p>
    <p class="mt-1 text-2xl font-bold text-destructive"><?= esc(rupiah($summary['unpaid'])) ?></p>
  </div>
  <div class="stat-card h-auto text-center">
    <p class="stat-card-label">Terkumpul</p>
    <p class="mt-1 text-2xl font-bold text-success"><?= esc(rupiah($summary['collected'])) ?></p>
  </div>
  <div class="stat-card h-auto text-center">
    <p class="stat-card-label">Penggantian Buku</p>
    <p class="mt-1 text-2xl font-bold text-warning"><?= esc((string) $summary['open_replacements']) ?></p>
  </div>
</div>

<div id="fine-settings-modal" class="<?= $settingsModalOpen ? '' : 'hidden' ?> fixed inset-0 z-50 flex items-center justify-center p-4">
  <div id="fine-settings-backdrop" class="absolute inset-0 bg-slate-950/35 backdrop-blur-sm"></div>
  <div class="panel-card relative z-10 w-full max-w-2xl p-6 lg:p-7">
    <div class="mb-4 flex items-start justify-between gap-4">
      <div>
        <h2 class="section-heading">Aturan Denda</h2>
        <p class="section-description">Atur nominal keterlambatan mingguan, masa tenggang, denda kerusakan, dan durasi pinjam default.</p>
      </div>
      <button type="button" id="fine-settings-close" class="panel-button-secondary px-3" aria-label="Tutup dialog aturan denda">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <line x1="6" y1="6" x2="18" y2="18"></line>
          <line x1="18" y1="6" x2="6" y2="18"></line>
        </svg>
      </button>
    </div>

    <form method="post" action="<?= site_url('fines/settings') ?>" class="grid grid-cols-1 gap-4">
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
      <div class="flex justify-end gap-3">
        <button type="button" id="fine-settings-cancel" class="panel-button-secondary">Tutup</button>
        <button type="submit" class="panel-button">Simpan Aturan</button>
      </div>
    </form>
  </div>
</div>

<form method="get" action="<?= site_url('fines') ?>" class="content-toolbar">
  <div class="grid grid-cols-1 gap-3 xl:grid-cols-[minmax(0,1.5fr)_minmax(0,220px)_minmax(0,220px)_auto] xl:items-end">
    <div class="relative">
      <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
      <input type="text" name="q" value="<?= esc($filters['q']) ?>" placeholder="Cari anggota, judul buku, atau kode copy..." class="panel-input pl-9">
    </div>

    <select name="type" class="panel-input">
      <option value="">Semua Jenis</option>
      <option value="late" <?= $filters['type'] === 'late' ? 'selected' : '' ?>>Keterlambatan</option>
      <option value="damage" <?= $filters['type'] === 'damage' ? 'selected' : '' ?>>Kerusakan Buku</option>
      <option value="lost" <?= $filters['type'] === 'lost' ? 'selected' : '' ?>>Kehilangan Buku</option>
    </select>

    <select name="status" class="panel-input">
      <option value="">Semua Status</option>
      <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Kasus Aktif</option>
      <option value="unpaid" <?= $filters['status'] === 'unpaid' ? 'selected' : '' ?>>Belum Lunas</option>
      <option value="partial" <?= $filters['status'] === 'partial' ? 'selected' : '' ?>>Cicil</option>
      <option value="paid" <?= $filters['status'] === 'paid' ? 'selected' : '' ?>>Lunas</option>
      <option value="open" <?= $filters['status'] === 'open' ? 'selected' : '' ?>>Menunggu Penggantian</option>
      <option value="resolved" <?= $filters['status'] === 'resolved' ? 'selected' : '' ?>>Selesai</option>
    </select>

    <div class="flex gap-2 xl:flex-nowrap xl:justify-end">
      <button type="submit" class="panel-button justify-center">Filter</button>
      <a href="<?= site_url('fines') ?>" class="panel-button-secondary justify-center">Reset</a>
    </div>
  </div>
</form>

<div class="space-y-4">
  <?php if ($fines === []): ?>
    <div class="empty-state">
      <h2 class="empty-state-title">Belum ada kasus denda</h2>
      <p class="empty-state-description">Denda akan muncul otomatis dari keterlambatan, kerusakan, atau laporan kehilangan buku. Coba ubah filter jika data yang dicari belum muncul.</p>
    </div>
  <?php else: ?>
    <div class="flex flex-col gap-2 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between">
      <p>Menampilkan <?= esc((string) $pagination['from']) ?>-<?= esc((string) $pagination['to']) ?> dari <?= esc((string) $pagination['total_rows']) ?> kasus.</p>
      <?php if ($filters['q'] !== '' || $filters['type'] !== '' || $filters['status'] !== ''): ?>
        <p>Filter aktif: <?= esc($filters['q'] !== '' ? 'pencarian' : '') ?><?= esc($filters['q'] !== '' && ($filters['type'] !== '' || $filters['status'] !== '') ? ', ' : '') ?><?= esc($filters['type'] !== '' ? fine_type_label($filters['type']) : '') ?><?= esc($filters['type'] !== '' && $filters['status'] !== '' ? ', ' : '') ?><?= esc(match ($filters['status']) { 'active' => 'Kasus Aktif', 'unpaid' => 'Belum Lunas', 'partial' => 'Cicil', 'paid' => 'Lunas', 'open' => 'Menunggu Penggantian', 'resolved' => 'Selesai', default => '', }) ?></p>
      <?php endif; ?>
    </div>

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

    <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
      <div class="content-toolbar">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <p class="text-sm text-slate-500">Halaman <?= esc((string) $pagination['page']) ?> dari <?= esc((string) $pagination['total_pages']) ?></p>
          <div class="flex flex-wrap gap-2">
            <?php
            $prevPage = max(1, (int) $pagination['page'] - 1);
            $nextPage = min((int) $pagination['total_pages'], (int) $pagination['page'] + 1);
            $startPage = max(1, (int) $pagination['page'] - 2);
            $endPage = min((int) $pagination['total_pages'], $startPage + 4);
            $startPage = max(1, $endPage - 4);
            ?>

            <a href="<?= site_url('fines' . ($pagination['page'] > 1 ? '?' . http_build_query($pageQueryBase + ['page' => $prevPage]) : (empty($pageQueryBase) ? '' : '?' . http_build_query($pageQueryBase)))) ?>" class="panel-button-secondary <?= $pagination['page'] <= 1 ? 'pointer-events-none opacity-50' : '' ?>">Sebelumnya</a>

            <?php for ($pageNumber = $startPage; $pageNumber <= $endPage; $pageNumber++): ?>
              <?php $pageUrl = site_url('fines' . '?' . http_build_query($pageQueryBase + ['page' => $pageNumber])); ?>
              <a href="<?= $pageUrl ?>" class="<?= $pageNumber === (int) $pagination['page'] ? 'panel-button' : 'panel-button-secondary' ?>">
                <?= esc((string) $pageNumber) ?>
              </a>
            <?php endfor; ?>

            <a href="<?= site_url('fines' . ($pagination['page'] < $pagination['total_pages'] ? '?' . http_build_query($pageQueryBase + ['page' => $nextPage]) : '?' . http_build_query($pageQueryBase + ['page' => $pagination['page']]))) ?>" class="panel-button-secondary <?= $pagination['page'] >= $pagination['total_pages'] ? 'pointer-events-none opacity-50' : '' ?>">Berikutnya</a>
          </div>
        </div>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageSkeleton') ?>
<?= $this->include('partials/skeletons/fines') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  const fineSettingsModal = document.getElementById('fine-settings-modal');
  const fineSettingsOpen = document.getElementById('fine-settings-open');
  const fineSettingsClose = document.getElementById('fine-settings-close');
  const fineSettingsCancel = document.getElementById('fine-settings-cancel');
  const fineSettingsBackdrop = document.getElementById('fine-settings-backdrop');

  const setFineSettingsModal = (isOpen) => {
    if (! fineSettingsModal) {
      return;
    }

    fineSettingsModal.classList.toggle('hidden', !isOpen);
    document.body.classList.toggle('overflow-hidden', isOpen);
  };

  fineSettingsOpen?.addEventListener('click', () => setFineSettingsModal(true));
  fineSettingsClose?.addEventListener('click', () => setFineSettingsModal(false));
  fineSettingsCancel?.addEventListener('click', () => setFineSettingsModal(false));
  fineSettingsBackdrop?.addEventListener('click', () => setFineSettingsModal(false));

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      setFineSettingsModal(false);
    }
  });
</script>
<?= $this->endSection() ?>
