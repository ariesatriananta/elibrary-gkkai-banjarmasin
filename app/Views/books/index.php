<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$pagination = $pagination ?? ['page' => 1, 'total_pages' => 1, 'total_rows' => 0, 'from' => 0, 'to' => 0];
$pageQueryBase = array_filter([
    'q' => $filters['q'] ?? '',
    'category_id' => $filters['category_id'] ?? '',
    'age_classification_id' => $filters['age_classification_id'] ?? '',
    'stock_status' => $filters['stock_status'] ?? '',
], static fn ($value): bool => $value !== '' && $value !== null);
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Data Buku</h1>
    <p class="page-description">Kelola judul buku, cover, kategori, klasifikasi usia, dan copy fisik perpustakaan.</p>
  </div>
  <a href="<?= site_url('books/create') ?>" class="panel-button">
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <line x1="12" y1="5" x2="12" y2="19"></line>
      <line x1="5" y1="12" x2="19" y2="12"></line>
    </svg>
    Tambah Buku
  </a>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
  <div class="stat-card">
    <p class="stat-card-label">Jumlah Judul</p>
    <p class="stat-card-value"><?= esc((string) $summary['titles']) ?></p>
  </div>
  <div class="stat-card">
    <p class="stat-card-label">Total Copy</p>
    <p class="stat-card-value"><?= esc((string) $summary['copies']) ?></p>
  </div>
  <div class="stat-card">
    <p class="stat-card-label">Copy Tersedia</p>
    <p class="stat-card-value text-success"><?= esc((string) $summary['available']) ?></p>
  </div>
  <div class="stat-card">
    <p class="stat-card-label">Copy Dipinjam</p>
    <p class="stat-card-value text-primary"><?= esc((string) $summary['borrowed']) ?></p>
  </div>
</div>

<form method="get" action="<?= site_url('books') ?>" class="content-toolbar">
  <div class="grid grid-cols-1 gap-3 lg:grid-cols-[1.5fr_0.8fr_0.8fr_0.7fr_auto]">
    <div class="relative">
      <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
      <input type="text" name="q" value="<?= esc($filters['q']) ?>" placeholder="Cari judul, penulis, ISBN, kode sistem, barcode..." class="panel-input pl-9">
    </div>

    <select name="category_id" class="panel-input">
      <option value="">Semua Kategori</option>
      <?php foreach ($categories as $category): ?>
        <option value="<?= esc((string) $category['id']) ?>" <?= $filters['category_id'] === (string) $category['id'] ? 'selected' : '' ?>>
          <?= esc($category['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select name="age_classification_id" class="panel-input">
      <option value="">Semua Klasifikasi</option>
      <?php foreach ($ageClassifications as $classification): ?>
        <option value="<?= esc((string) $classification['id']) ?>" <?= $filters['age_classification_id'] === (string) $classification['id'] ? 'selected' : '' ?>>
          <?= esc($classification['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select name="stock_status" class="panel-input">
      <option value="">Semua Status</option>
      <option value="available" <?= $filters['stock_status'] === 'available' ? 'selected' : '' ?>>Ada yang tersedia</option>
      <option value="borrowed" <?= $filters['stock_status'] === 'borrowed' ? 'selected' : '' ?>>Semua dipinjam</option>
    </select>

    <div class="flex gap-2">
      <button type="submit" class="panel-button justify-center">Filter</button>
      <a href="<?= site_url('books') ?>" class="panel-button-secondary justify-center">Reset</a>
    </div>
  </div>
</form>

<?php if ($books === []): ?>
  <div class="empty-state">
    <h2 class="empty-state-title">Data buku tidak ditemukan</h2>
    <p class="empty-state-description">Coba ubah filter pencarian atau tambahkan buku baru.</p>
  </div>
<?php else: ?>
  <div class="flex flex-col gap-2 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between">
    <p>Menampilkan <?= esc((string) $pagination['from']) ?>-<?= esc((string) $pagination['to']) ?> dari <?= esc((string) $pagination['total_rows']) ?> judul buku.</p>
    <?php if ($filters['q'] !== '' || $filters['category_id'] !== '' || $filters['age_classification_id'] !== '' || $filters['stock_status'] !== ''): ?>
      <p>Filter aktif pada hasil buku.</p>
    <?php endif; ?>
  </div>

  <div class="books-card-grid">
    <?php foreach ($books as $book): ?>
      <div class="panel-card book-card overflow-hidden">
        <div class="book-card-cover flex items-center justify-center bg-gradient-to-br <?= esc($book['cover_class']) ?>">
          <?php if (! empty($book['cover_path'])): ?>
            <img src="<?= base_url($book['cover_path']) ?>" alt="<?= esc($book['title']) ?>" class="h-full w-full object-cover">
          <?php else: ?>
            <span class="px-4 text-center text-base font-bold leading-tight text-white drop-shadow"><?= esc($book['title']) ?></span>
          <?php endif; ?>
        </div>

        <div class="book-card-body">
          <div class="space-y-1">
            <h2 class="line-clamp-2 text-base font-semibold leading-snug"><?= esc($book['title']) ?></h2>
            <p class="line-clamp-1 text-sm text-slate-500"><?= esc($book['author']) ?></p>
          </div>

          <div class="flex flex-wrap gap-2">
            <?php if (! empty($book['category_name'])): ?>
              <span class="surface-chip surface-chip-primary"><?= esc($book['category_name']) ?></span>
            <?php endif; ?>

            <?php if (! empty($book['age_classification_name'])): ?>
              <span class="surface-chip surface-chip-info"><?= esc($book['age_classification_name']) ?></span>
            <?php endif; ?>

            <span class="status-badge <?= esc($book['stock_status_class']) ?>">
              <?= esc($book['stock_status_label']) ?>
            </span>
          </div>

          <div class="book-card-stats">
            <div class="metric-tile text-center">
              <p class="text-[11px] text-slate-500">Copy</p>
              <p class="mt-1.5 text-base font-semibold"><?= esc((string) $book['total_copies']) ?></p>
            </div>
            <div class="metric-tile text-center">
              <p class="text-[11px] text-slate-500">Tersedia</p>
              <p class="mt-1.5 text-base font-semibold text-success"><?= esc((string) $book['available_copies']) ?></p>
            </div>
            <div class="metric-tile text-center">
              <p class="text-[11px] text-slate-500">Dipinjam</p>
              <p class="mt-1.5 text-base font-semibold text-primary"><?= esc((string) $book['borrowed_copies']) ?></p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3 text-sm">
            <div>
              <p class="text-xs uppercase tracking-wide text-slate-400">ISBN</p>
              <p class="mt-1 line-clamp-1 text-sm"><?= esc($book['isbn'] ?: '-') ?></p>
            </div>
            <div>
              <p class="text-xs uppercase tracking-wide text-slate-400">Rak</p>
              <p class="mt-1 line-clamp-1 text-sm"><?= esc($book['shelf_location'] ?: '-') ?></p>
            </div>
          </div>

          <div class="flex items-center justify-end gap-2">
            <a href="<?= site_url('books/' . $book['id'] . '/edit') ?>" class="book-card-action-button" title="Kelola buku" aria-label="Kelola buku <?= esc($book['title']) ?>">
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="11" cy="11" r="7"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              </svg>
            </a>
            <form method="post" action="<?= site_url('books/' . $book['id'] . '/delete') ?>" onsubmit="return confirm('Hapus buku ini? Semua copy tanpa histori transaksi juga akan ikut terhapus.');">
              <button type="submit" class="book-card-action-button book-card-action-button-danger" title="Hapus buku" aria-label="Hapus buku <?= esc($book['title']) ?>">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  <polyline points="3 6 5 6 21 6"></polyline>
                  <path d="M19 6l-1 14H6L5 6"></path>
                  <path d="M10 11v6"></path>
                  <path d="M14 11v6"></path>
                  <path d="M9 6V4h6v2"></path>
                </svg>
              </button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
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

          <a href="<?= site_url('books' . ($pagination['page'] > 1 ? '?' . http_build_query($pageQueryBase + ['page' => $prevPage]) : (empty($pageQueryBase) ? '' : '?' . http_build_query($pageQueryBase)))) ?>" class="panel-button-secondary <?= $pagination['page'] <= 1 ? 'pointer-events-none opacity-50' : '' ?>">Sebelumnya</a>

          <?php for ($pageNumber = $startPage; $pageNumber <= $endPage; $pageNumber++): ?>
            <?php $pageUrl = site_url('books' . '?' . http_build_query($pageQueryBase + ['page' => $pageNumber])); ?>
            <a href="<?= $pageUrl ?>" class="<?= $pageNumber === (int) $pagination['page'] ? 'panel-button' : 'panel-button-secondary' ?>">
              <?= esc((string) $pageNumber) ?>
            </a>
          <?php endfor; ?>

          <a href="<?= site_url('books' . ($pagination['page'] < $pagination['total_pages'] ? '?' . http_build_query($pageQueryBase + ['page' => $nextPage]) : '?' . http_build_query($pageQueryBase + ['page' => $pagination['page']]))) ?>" class="panel-button-secondary <?= $pagination['page'] >= $pagination['total_pages'] ? 'pointer-events-none opacity-50' : '' ?>">Berikutnya</a>
        </div>
      </div>
    </div>
  <?php endif; ?>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('pageSkeleton') ?>
<?= $this->include('partials/skeletons/books') ?>
<?= $this->endSection() ?>
