<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$isEdit = $mode === 'edit';
$bookId = $book['id'] ?? null;
$formAction = $isEdit ? site_url('books/' . $bookId) : site_url('books');
$errors = $errors ?? [];
?>

<div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
  <div>
    <h1 class="text-2xl font-bold tracking-tight"><?= $isEdit ? 'Edit Buku' : 'Tambah Buku' ?></h1>
    <p class="text-slate-500">
      <?= $isEdit ? 'Perbarui metadata buku dan kelola copy fisiknya.' : 'Tambahkan judul baru beserta jumlah copy awal.' ?>
    </p>
  </div>
  <a href="<?= site_url('books') ?>" class="panel-button-secondary w-fit">Kembali ke Data Buku</a>
</div>

<?php if ($errors !== []): ?>
  <div class="rounded-xl border border-destructive/20 bg-destructive/10 px-4 py-3 text-sm text-destructive">
    <p class="font-medium">Periksa kembali form buku.</p>
    <ul class="mt-2 list-disc pl-5">
      <?php foreach ($errors as $error): ?>
        <li><?= esc($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.2fr_0.8fr]">
  <form method="post" action="<?= $formAction ?>" enctype="multipart/form-data" class="space-y-6">
    <div class="panel-card p-6">
      <h2 class="mb-4 text-lg font-semibold">Informasi Buku</h2>

      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div class="md:col-span-2">
          <label for="title" class="mb-1 block text-sm font-medium">Judul Buku</label>
          <input id="title" name="title" type="text" value="<?= esc(old('title', $book['title'] ?? '')) ?>" class="panel-input" required>
        </div>

        <div>
          <label for="author" class="mb-1 block text-sm font-medium">Pengarang</label>
          <input id="author" name="author" type="text" value="<?= esc(old('author', $book['author'] ?? '')) ?>" class="panel-input" required>
        </div>

        <div>
          <label for="publisher" class="mb-1 block text-sm font-medium">Penerbit</label>
          <input id="publisher" name="publisher" type="text" value="<?= esc(old('publisher', $book['publisher'] ?? '')) ?>" class="panel-input">
        </div>

        <div>
          <label for="category_id" class="mb-1 block text-sm font-medium">Kategori</label>
          <select id="category_id" name="category_id" class="panel-input">
            <option value="">Pilih kategori</option>
            <?php foreach ($categories as $category): ?>
              <?php $selectedCategory = old('category_id', $book['category_id'] ?? ''); ?>
              <option value="<?= esc((string) $category['id']) ?>" <?= (string) $selectedCategory === (string) $category['id'] ? 'selected' : '' ?>>
                <?= esc($category['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label for="age_classification_id" class="mb-1 block text-sm font-medium">Klasifikasi Usia</label>
          <select id="age_classification_id" name="age_classification_id" class="panel-input">
            <option value="">Pilih klasifikasi</option>
            <?php foreach ($ageClassifications as $classification): ?>
              <?php $selectedClassification = old('age_classification_id', $book['age_classification_id'] ?? ''); ?>
              <option value="<?= esc((string) $classification['id']) ?>" <?= (string) $selectedClassification === (string) $classification['id'] ? 'selected' : '' ?>>
                <?= esc($classification['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label for="isbn" class="mb-1 block text-sm font-medium">ISBN / Kode Referensi</label>
          <input id="isbn" name="isbn" type="text" value="<?= esc(old('isbn', $book['isbn'] ?? '')) ?>" class="panel-input">
        </div>

        <div>
          <label for="publication_year" class="mb-1 block text-sm font-medium">Tahun Terbit</label>
          <input id="publication_year" name="publication_year" type="number" value="<?= esc(old('publication_year', $book['publication_year'] ?? '')) ?>" class="panel-input" min="1000" max="2100">
        </div>

        <div>
          <label for="page_count" class="mb-1 block text-sm font-medium">Jumlah Halaman</label>
          <input id="page_count" name="page_count" type="number" value="<?= esc(old('page_count', $book['page_count'] ?? '')) ?>" class="panel-input" min="1">
        </div>

        <div>
          <label for="shelf_location" class="mb-1 block text-sm font-medium">No Rak / Lokasi</label>
          <input id="shelf_location" name="shelf_location" type="text" value="<?= esc(old('shelf_location', $book['shelf_location'] ?? '')) ?>" class="panel-input">
        </div>

        <div>
          <label for="legacy_status" class="mb-1 block text-sm font-medium">Status Data Lama</label>
          <input id="legacy_status" name="legacy_status" type="text" value="<?= esc(old('legacy_status', $book['legacy_status'] ?? '')) ?>" class="panel-input" placeholder="Contoh: BUKU BARU">
        </div>

        <?php if (! $isEdit): ?>
          <div>
            <label for="initial_copies" class="mb-1 block text-sm font-medium">Jumlah Copy Awal</label>
            <input id="initial_copies" name="initial_copies" type="number" value="<?= esc(old('initial_copies', '1')) ?>" class="panel-input" min="1" max="100">
          </div>
        <?php endif; ?>

        <div class="md:col-span-2">
          <label for="synopsis" class="mb-1 block text-sm font-medium">Sinopsis</label>
          <textarea id="synopsis" name="synopsis" rows="5" class="panel-input"><?= esc(old('synopsis', $book['synopsis'] ?? '')) ?></textarea>
        </div>
      </div>
    </div>

    <div class="panel-card p-6">
      <h2 class="mb-4 text-lg font-semibold">Cover Buku</h2>

      <div class="space-y-4">
        <div class="overflow-hidden rounded-2xl border border-border bg-muted">
          <?php if (! empty($book['cover_path'])): ?>
            <img src="<?= base_url($book['cover_path']) ?>" alt="<?= esc($book['title'] ?? 'Cover Buku') ?>" class="h-72 w-full object-cover">
          <?php else: ?>
            <div class="flex h-72 items-center justify-center bg-gradient-to-br from-primary/80 to-accent/50 p-6 text-center text-white">
              <div>
                <p class="text-sm uppercase tracking-[0.3em] text-white/70">Elibrary GKKAI</p>
                <p class="mt-3 text-xl font-bold"><?= esc(old('title', $book['title'] ?? 'Belum ada cover')) ?></p>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <div>
          <label for="cover" class="mb-1 block text-sm font-medium">Upload Cover Baru</label>
          <input id="cover" name="cover" type="file" accept=".jpg,.jpeg,.png,.webp" class="panel-input">
          <p class="mt-1 text-xs text-slate-500">Format JPG, PNG, WEBP. Maksimal 2MB.</p>
        </div>

        <?php if ($isEdit && ! empty($book['cover_path'])): ?>
          <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remove_cover" value="1" class="h-4 w-4 rounded border-border text-primary focus:ring-primary/20">
            Hapus cover saat menyimpan perubahan
          </label>
        <?php endif; ?>
      </div>
    </div>

    <div class="flex gap-3">
      <button type="submit" class="panel-button flex-1 justify-center">
        <?= $isEdit ? 'Simpan Perubahan' : 'Simpan Buku' ?>
      </button>
      <a href="<?= site_url('books') ?>" class="panel-button-secondary flex-1 justify-center">Batal</a>
    </div>
  </form>

  <div class="space-y-6">
    <?php if ($isEdit): ?>
      <div class="panel-card p-6">
        <h2 class="mb-4 text-lg font-semibold">Ringkasan Buku</h2>
        <div class="space-y-3 text-sm">
          <div class="flex items-center justify-between">
            <span class="text-slate-500">ID Buku</span>
            <span class="font-medium">#<?= esc((string) $bookId) ?></span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-slate-500">Jumlah Copy</span>
            <span class="font-medium"><?= esc((string) count($copies)) ?></span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-slate-500">Cover</span>
            <span class="font-medium"><?= ! empty($book['cover_path']) ? 'Ada' : 'Belum ada' ?></span>
          </div>
          <div class="flex items-center justify-between">
            <span class="text-slate-500">Dibuat</span>
            <span class="font-medium"><?= esc((string) ($book['created_at'] ?? '-')) ?></span>
          </div>
        </div>
      </div>

      <form method="post" action="<?= site_url('books/' . $bookId . '/delete') ?>" class="panel-card p-6" onsubmit="return confirm('Hapus buku ini? Semua copy tanpa histori transaksi juga akan ikut terhapus.');">
        <h2 class="text-lg font-semibold text-destructive">Hapus Buku</h2>
        <p class="mt-2 text-sm text-slate-500">Gunakan hanya jika buku ini memang harus dihapus dari sistem.</p>
        <button type="submit" class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-destructive px-4 py-2 text-sm font-medium text-white hover:opacity-90">
          Hapus Buku
        </button>
      </form>
    <?php else: ?>
      <div class="panel-card p-6">
        <h2 class="mb-3 text-lg font-semibold">Catatan</h2>
        <p class="text-sm leading-6 text-slate-500">
          Setelah buku dibuat, sistem akan membuat kode copy otomatis seperti <span class="font-medium text-foreground">BK-000123-01</span>.
          Barcode manual dan kode buku lama bisa diatur setelah buku tersimpan.
        </p>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php if ($isEdit): ?>
  <div class="panel-card p-6">
    <div class="mb-4">
      <h2 class="text-lg font-semibold">Copy Buku</h2>
      <p class="text-sm text-slate-500">Kode sistem dibuat otomatis. Barcode manual dan kode lama bisa diubah per copy.</p>
    </div>

    <div class="mb-6 overflow-x-auto">
      <table class="w-full border-collapse">
        <thead>
          <tr class="text-left text-xs font-medium text-slate-500">
            <th class="border-b border-border px-3 py-3">Kode Sistem</th>
            <th class="border-b border-border px-3 py-3">Kode Lama</th>
            <th class="border-b border-border px-3 py-3">Barcode Manual</th>
            <th class="border-b border-border px-3 py-3">Status</th>
            <th class="border-b border-border px-3 py-3">Catatan</th>
            <th class="border-b border-border px-3 py-3">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($copies === []): ?>
            <tr>
              <td colspan="6" class="border-b border-border px-3 py-6 text-center text-sm text-slate-500">
                Belum ada copy buku untuk judul ini.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($copies as $copy): ?>
              <tr class="align-top text-sm">
                <td class="border-b border-border px-3 py-3 font-medium"><?= esc($copy['copy_code']) ?></td>
                <td class="border-b border-border px-3 py-3" colspan="4">
                  <form method="post" action="<?= site_url('books/' . $bookId . '/copies/' . $copy['id']) ?>" class="grid grid-cols-1 gap-3 lg:grid-cols-[0.9fr_1fr_0.8fr_1.2fr_auto]">
                    <input type="text" name="legacy_code" value="<?= esc($copy['legacy_code'] ?? '') ?>" class="panel-input" placeholder="Kode lama">
                    <input type="text" name="barcode_value" value="<?= esc($copy['barcode_value'] ?? '') ?>" class="panel-input" placeholder="Barcode manual">
                    <select name="status" class="panel-input">
                      <?php foreach ($copyStatuses as $statusValue => $statusLabel): ?>
                        <option value="<?= esc($statusValue) ?>" <?= $copy['status'] === $statusValue ? 'selected' : '' ?>>
                          <?= esc($statusLabel) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                    <input type="text" name="notes" value="<?= esc($copy['notes'] ?? '') ?>" class="panel-input" placeholder="Catatan copy">
                    <button type="submit" class="panel-button-secondary justify-center">Simpan</button>
                  </form>
                </td>
                <td class="border-b border-border px-3 py-3">
                  <form method="post" action="<?= site_url('books/' . $bookId . '/copies/' . $copy['id'] . '/delete') ?>" onsubmit="return confirm('Hapus copy buku ini?');">
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-destructive px-4 py-2 text-sm font-medium text-white hover:opacity-90">
                      Hapus
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="rounded-2xl border border-dashed border-border p-4">
      <h3 class="text-base font-semibold">Tambah Copy Baru</h3>
      <p class="mb-4 mt-1 text-sm text-slate-500">Sistem akan membuat kode copy baru otomatis.</p>

      <form method="post" action="<?= site_url('books/' . $bookId . '/copies') ?>" class="grid grid-cols-1 gap-3 lg:grid-cols-[0.8fr_1fr_0.8fr_1.2fr_auto]">
        <input type="text" name="legacy_code" class="panel-input" placeholder="Kode lama">
        <input type="text" name="barcode_value" class="panel-input" placeholder="Barcode manual">
        <select name="status" class="panel-input">
          <?php foreach ($copyStatuses as $statusValue => $statusLabel): ?>
            <option value="<?= esc($statusValue) ?>"><?= esc($statusLabel) ?></option>
          <?php endforeach; ?>
        </select>
        <input type="text" name="notes" class="panel-input" placeholder="Catatan copy">
        <button type="submit" class="panel-button justify-center">Tambah Copy</button>
      </form>
    </div>
  </div>
<?php endif; ?>
<?= $this->endSection() ?>
