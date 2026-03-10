<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex items-center justify-between gap-4">
  <div>
    <h1 class="text-2xl font-bold tracking-tight">Data Buku</h1>
    <p class="text-slate-500">Kelola koleksi buku dan copy fisik perpustakaan.</p>
  </div>
  <button class="panel-button" type="button">
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <line x1="12" y1="5" x2="12" y2="19"></line>
      <line x1="5" y1="12" x2="19" y2="12"></line>
    </svg>
    Tambah Buku
  </button>
</div>

<div class="flex flex-col gap-3 lg:flex-row">
  <div class="relative flex-1">
    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <circle cx="11" cy="11" r="8"></circle>
      <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
    </svg>
    <input type="text" placeholder="Cari judul, penulis, atau kode buku..." class="panel-input pl-9">
  </div>
  <select class="panel-input w-full lg:w-64">
    <?php foreach ($categories as $category): ?>
      <option><?= esc($category) ?></option>
    <?php endforeach; ?>
  </select>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
  <?php foreach ($books as $book): ?>
    <div class="panel-card overflow-hidden transition-shadow hover:shadow-lg">
      <div class="flex h-32 items-center justify-center bg-gradient-to-br <?= esc($book['coverClass']) ?>">
        <span class="px-4 text-center text-lg font-bold leading-tight text-white drop-shadow"><?= esc($book['title']) ?></span>
      </div>
      <div class="space-y-2 p-4">
        <p class="text-xs text-slate-500"><?= esc($book['author']) ?></p>
        <div class="flex items-center justify-between gap-3">
          <span class="rounded-full border border-border bg-primary/10 px-2 py-1 text-xs text-primary"><?= esc($book['category']) ?></span>
          <span class="text-xs text-slate-500"><?= esc($book['available']) ?></span>
        </div>
        <?php if ($book['allBorrowed']): ?>
          <span class="status-badge status-badge-overdue">Semua Dipinjam</span>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?= $this->endSection() ?>
