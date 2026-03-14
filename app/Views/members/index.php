<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$pagination = $pagination ?? ['page' => 1, 'total_pages' => 1, 'total_rows' => 0, 'from' => 0, 'to' => 0];
$pageQueryBase = array_filter([
    'q' => $filters['q'] ?? '',
    'status' => $filters['status'] ?? '',
], static fn ($value): bool => $value !== '' && $value !== null);
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Data Anggota</h1>
    <p class="page-description">Kelola anggota, status aktif, dan riwayat peminjaman.</p>
  </div>
  <div class="flex flex-wrap items-center justify-end gap-2">
    <a href="<?= site_url('members/export' . (empty($pageQueryBase) ? '' : '?' . http_build_query($pageQueryBase))) ?>" class="panel-button-secondary" data-no-loading="true">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M12 3v12"></path>
        <path d="m7 10 5 5 5-5"></path>
        <path d="M5 21h14"></path>
      </svg>
      Export Excel
    </a>
    <a href="<?= site_url('members/create') ?>" class="panel-button">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <line x1="12" y1="5" x2="12" y2="19"></line>
        <line x1="5" y1="12" x2="19" y2="12"></line>
      </svg>
      Tambah Anggota
    </a>
  </div>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
  <div class="stat-card">
    <p class="stat-card-label">Total Anggota</p>
    <p class="stat-card-value"><?= esc((string) $summary['total']) ?></p>
  </div>
  <div class="stat-card">
    <p class="stat-card-label">Aktif</p>
    <p class="stat-card-value text-success"><?= esc((string) $summary['active']) ?></p>
  </div>
  <div class="stat-card">
    <p class="stat-card-label">Nonaktif</p>
    <p class="stat-card-value text-slate-600"><?= esc((string) $summary['inactive']) ?></p>
  </div>
  <div class="stat-card">
    <p class="stat-card-label">Pinjaman Aktif</p>
    <p class="stat-card-value text-primary"><?= esc((string) $summary['loans']) ?></p>
  </div>
</div>

<form method="get" action="<?= site_url('members') ?>" class="content-toolbar">
  <div class="grid grid-cols-1 gap-3 lg:grid-cols-[1.4fr_0.8fr_auto]">
    <div class="relative">
      <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
      <input type="text" name="q" value="<?= esc($filters['q']) ?>" placeholder="Cari nama, nomor anggota, telepon, email..." class="panel-input pl-9">
    </div>

    <select name="status" class="panel-input">
      <option value="">Semua Status</option>
      <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Aktif</option>
      <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
    </select>

    <div class="flex gap-2">
      <button type="submit" class="panel-button justify-center">Filter</button>
      <a href="<?= site_url('members') ?>" class="panel-button-secondary justify-center">Reset</a>
    </div>
  </div>
</form>

<?php if ($members === []): ?>
  <div class="empty-state">
    <h2 class="empty-state-title">Belum ada anggota</h2>
    <p class="empty-state-description">Tambahkan anggota perpustakaan terlebih dahulu agar transaksi peminjaman bisa dilakukan.</p>
  </div>
<?php else: ?>
  <div class="flex flex-col gap-2 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between">
    <p>Menampilkan <?= esc((string) $pagination['from']) ?>-<?= esc((string) $pagination['to']) ?> dari <?= esc((string) $pagination['total_rows']) ?> anggota.</p>
    <?php if (($filters['q'] ?? '') !== '' || ($filters['status'] ?? '') !== ''): ?>
      <p>Filter aktif pada data anggota.</p>
    <?php endif; ?>
  </div>

  <div class="data-table-wrapper">
    <div class="overflow-x-auto">
      <table class="data-table">
        <thead>
          <tr>
            <th>Anggota</th>
            <th>Kontak</th>
            <th>Bergabung</th>
            <th>Status</th>
            <th>Pinjaman Aktif</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($members as $member): ?>
            <tr>
              <td>
                <div class="flex items-center gap-3">
                  <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-xs font-semibold text-primary">
                    <?= esc($member['initials']) ?>
                  </div>
                  <div>
                    <p class="font-medium"><?= esc($member['full_name']) ?></p>
                    <p class="text-xs text-slate-500"><?= esc($member['member_number']) ?></p>
                  </div>
                </div>
              </td>
              <td class="text-slate-500">
                <p><?= esc($member['phone'] ?: '-') ?></p>
                <p class="text-xs"><?= esc($member['email'] ?: '-') ?></p>
              </td>
              <td class="text-slate-500"><?= esc($member['joined_at'] ? format_indo_date($member['joined_at']) : '-') ?></td>
              <td>
                <span class="status-badge <?= (int) $member['is_active'] === 1 ? 'status-badge-available' : 'status-badge-neutral' ?>">
                  <?= (int) $member['is_active'] === 1 ? 'Aktif' : 'Nonaktif' ?>
                </span>
              </td>
              <td>
                <span class="status-badge <?= $member['active_loans'] > 0 ? 'status-badge-borrowed' : 'status-badge-neutral' ?>">
                  <?= esc((string) $member['active_loans']) ?>
                </span>
              </td>
              <td>
                <div class="flex gap-2">
                  <a href="<?= site_url('members/' . $member['id'] . '/edit') ?>" class="panel-button-secondary">Kelola</a>
                  <form method="post" action="<?= site_url('members/' . $member['id'] . '/delete') ?>" onsubmit="return confirm('Hapus anggota ini?');">
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-destructive px-3 py-2 text-xs font-medium text-white hover:opacity-90">
                      Hapus
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

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

          <a href="<?= site_url('members' . ($pagination['page'] > 1 ? '?' . http_build_query($pageQueryBase + ['page' => $prevPage]) : (empty($pageQueryBase) ? '' : '?' . http_build_query($pageQueryBase)))) ?>" class="panel-button-secondary <?= $pagination['page'] <= 1 ? 'pointer-events-none opacity-50' : '' ?>">Sebelumnya</a>

          <?php for ($pageNumber = $startPage; $pageNumber <= $endPage; $pageNumber++): ?>
            <?php $pageUrl = site_url('members' . '?' . http_build_query($pageQueryBase + ['page' => $pageNumber])); ?>
            <a href="<?= $pageUrl ?>" class="<?= $pageNumber === (int) $pagination['page'] ? 'panel-button' : 'panel-button-secondary' ?>">
              <?= esc((string) $pageNumber) ?>
            </a>
          <?php endfor; ?>

          <a href="<?= site_url('members' . ($pagination['page'] < $pagination['total_pages'] ? '?' . http_build_query($pageQueryBase + ['page' => $nextPage]) : '?' . http_build_query($pageQueryBase + ['page' => $pagination['page']]))) ?>" class="panel-button-secondary <?= $pagination['page'] >= $pagination['total_pages'] ? 'pointer-events-none opacity-50' : '' ?>">Berikutnya</a>
        </div>
      </div>
    </div>
  <?php endif; ?>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('pageSkeleton') ?>
<?= $this->include('partials/skeletons/members') ?>
<?= $this->endSection() ?>
