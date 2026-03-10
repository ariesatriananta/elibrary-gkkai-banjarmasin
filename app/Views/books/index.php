<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
  <div>
    <h1 class="text-2xl font-bold tracking-tight">Data Buku</h1>
    <p class="text-slate-500">Kelola judul buku, cover, kategori, klasifikasi usia, dan copy fisik perpustakaan.</p>
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
  <div class="panel-card p-5">
    <p class="text-sm text-slate-500">Jumlah Judul</p>
    <p class="mt-1 text-3xl font-bold"><?= esc((string) $summary['titles']) ?></p>
  </div>
  <div class="panel-card p-5">
    <p class="text-sm text-slate-500">Total Copy</p>
    <p class="mt-1 text-3xl font-bold"><?= esc((string) $summary['copies']) ?></p>
  </div>
  <div class="panel-card p-5">
    <p class="text-sm text-slate-500">Copy Tersedia</p>
    <p class="mt-1 text-3xl font-bold text-success"><?= esc((string) $summary['available']) ?></p>
  </div>
  <div class="panel-card p-5">
    <p class="text-sm text-slate-500">Copy Dipinjam</p>
    <p class="mt-1 text-3xl font-bold text-primary"><?= esc((string) $summary['borrowed']) ?></p>
  </div>
</div>

<form method="get" action="<?= site_url('books') ?>" class="panel-card p-4">
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
  <div class="panel-card p-8 text-center">
    <h2 class="text-lg font-semibold">Data buku tidak ditemukan</h2>
    <p class="mt-2 text-sm text-slate-500">Coba ubah filter pencarian atau tambahkan buku baru.</p>
  </div>
<?php else: ?>
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
    <?php foreach ($books as $book): ?>
      <div class="panel-card overflow-hidden">
        <div class="flex h-40 items-center justify-center bg-gradient-to-br <?= esc($book['cover_class']) ?>">
          <?php if (! empty($book['cover_path'])): ?>
            <img src="<?= base_url($book['cover_path']) ?>" alt="<?= esc($book['title']) ?>" class="h-full w-full object-cover">
          <?php else: ?>
            <span class="px-5 text-center text-lg font-bold leading-tight text-white drop-shadow"><?= esc($book['title']) ?></span>
          <?php endif; ?>
        </div>

        <div class="space-y-4 p-4">
          <div class="space-y-1">
            <h2 class="line-clamp-2 text-lg font-semibold"><?= esc($book['title']) ?></h2>
            <p class="text-sm text-slate-500"><?= esc($book['author']) ?></p>
          </div>

          <div class="flex flex-wrap gap-2">
            <?php if (! empty($book['category_name'])): ?>
              <span class="rounded-full border border-border bg-primary/10 px-2 py-1 text-xs text-primary"><?= esc($book['category_name']) ?></span>
            <?php endif; ?>

            <?php if (! empty($book['age_classification_name'])): ?>
              <span class="rounded-full border border-border bg-info/10 px-2 py-1 text-xs text-info"><?= esc($book['age_classification_name']) ?></span>
            <?php endif; ?>

            <span class="status-badge <?= esc($book['stock_status_class']) ?>">
              <?= esc($book['stock_status_label']) ?>
            </span>
          </div>

          <div class="grid grid-cols-3 gap-3 rounded-xl bg-muted p-3 text-center">
            <div>
              <p class="text-xs text-slate-500">Copy</p>
              <p class="text-lg font-semibold"><?= esc((string) $book['total_copies']) ?></p>
            </div>
            <div>
              <p class="text-xs text-slate-500">Tersedia</p>
              <p class="text-lg font-semibold text-success"><?= esc((string) $book['available_copies']) ?></p>
            </div>
            <div>
              <p class="text-xs text-slate-500">Dipinjam</p>
              <p class="text-lg font-semibold text-primary"><?= esc((string) $book['borrowed_copies']) ?></p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3 text-sm">
            <div>
              <p class="text-xs uppercase tracking-wide text-slate-400">ISBN</p>
              <p class="mt-1 line-clamp-1"><?= esc($book['isbn'] ?: '-') ?></p>
            </div>
            <div>
              <p class="text-xs uppercase tracking-wide text-slate-400">Rak</p>
              <p class="mt-1 line-clamp-1"><?= esc($book['shelf_location'] ?: '-') ?></p>
            </div>
          </div>

          <div class="flex gap-2">
            <a href="<?= site_url('books/' . $book['id'] . '/edit') ?>" class="panel-button-secondary flex-1 justify-center">Kelola</a>
            <form method="post" action="<?= site_url('books/' . $book['id'] . '/delete') ?>" class="flex-1" onsubmit="return confirm('Hapus buku ini? Semua copy tanpa histori transaksi juga akan ikut terhapus.');">
              <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-destructive px-4 py-2 text-sm font-medium text-white hover:opacity-90">
                Hapus
              </button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<?= $this->endSection() ?>
