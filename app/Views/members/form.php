<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$isEdit = $mode === 'edit';
$memberId = $member['id'] ?? null;
$errors = $errors ?? [];
$historyPagination = $historyPagination ?? ['page' => 1, 'total_pages' => 1, 'total_rows' => 0, 'from' => 0, 'to' => 0];
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><?= $isEdit ? 'Edit Anggota' : 'Tambah Anggota' ?></h1>
    <p class="page-description">Kelola identitas, kontak, status aktif, dan riwayat peminjaman anggota.</p>
  </div>
  <a href="<?= site_url('members') ?>" class="panel-button-secondary w-fit">Kembali ke Data Anggota</a>
</div>

<?php if ($errors !== []): ?>
  <div class="rounded-xl border border-destructive/20 bg-destructive/10 px-4 py-3 text-sm text-destructive">
    <p class="font-medium">Periksa kembali data anggota.</p>
    <ul class="mt-2 list-disc pl-5">
      <?php foreach ($errors as $error): ?>
        <li><?= esc($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.1fr_0.9fr]">
  <form method="post" action="<?= $isEdit ? site_url('members/' . $memberId) : site_url('members') ?>" class="space-y-6">
    <div class="panel-card p-6">
      <h2 class="mb-4 text-lg font-semibold">Profil Anggota</h2>

      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
          <label class="mb-1 block text-sm font-medium">Nomor Anggota</label>
          <input type="text" value="<?= esc($member['member_number']) ?>" class="panel-input bg-slate-50" readonly>
        </div>

        <div>
          <label for="joined_at" class="mb-1 block text-sm font-medium">Tanggal Bergabung</label>
          <input id="joined_at" name="joined_at" type="date" value="<?= esc(old('joined_at', $member['joined_at'] ?? date('Y-m-d'))) ?>" class="panel-input <?= field_error_class($errors, 'joined_at') ?>">
          <?php if (field_error($errors, 'joined_at')): ?>
            <p class="field-error mt-1"><?= esc(field_error($errors, 'joined_at')) ?></p>
          <?php endif; ?>
        </div>

        <div class="md:col-span-2">
          <label for="full_name" class="mb-1 block text-sm font-medium">Nama Lengkap</label>
          <input id="full_name" name="full_name" type="text" value="<?= esc(old('full_name', $member['full_name'] ?? '')) ?>" class="panel-input <?= field_error_class($errors, 'full_name') ?>" required>
          <?php if (field_error($errors, 'full_name')): ?>
            <p class="field-error mt-1"><?= esc(field_error($errors, 'full_name')) ?></p>
          <?php endif; ?>
        </div>

        <div>
          <label for="phone" class="mb-1 block text-sm font-medium">Telepon</label>
          <input id="phone" name="phone" type="text" value="<?= esc(old('phone', $member['phone'] ?? '')) ?>" class="panel-input <?= field_error_class($errors, 'phone') ?>">
          <?php if (field_error($errors, 'phone')): ?>
            <p class="field-error mt-1"><?= esc(field_error($errors, 'phone')) ?></p>
          <?php endif; ?>
        </div>

        <div>
          <label for="email" class="mb-1 block text-sm font-medium">Email</label>
          <input id="email" name="email" type="email" value="<?= esc(old('email', $member['email'] ?? '')) ?>" class="panel-input <?= field_error_class($errors, 'email') ?>">
          <?php if (field_error($errors, 'email')): ?>
            <p class="field-error mt-1"><?= esc(field_error($errors, 'email')) ?></p>
          <?php endif; ?>
        </div>

        <div class="md:col-span-2">
          <label for="address" class="mb-1 block text-sm font-medium">Alamat</label>
          <textarea id="address" name="address" rows="3" class="panel-input"><?= esc(old('address', $member['address'] ?? '')) ?></textarea>
        </div>

        <div class="md:col-span-2">
          <label for="notes" class="mb-1 block text-sm font-medium">Catatan</label>
          <textarea id="notes" name="notes" rows="4" class="panel-input"><?= esc(old('notes', $member['notes'] ?? '')) ?></textarea>
        </div>

        <div class="md:col-span-2">
          <?php $selectedActive = old('is_active', (string) ($member['is_active'] ?? '1')); ?>
          <label class="mb-1 block text-sm font-medium">Status Anggota</label>
          <div class="flex gap-3">
            <label class="option-card">
              <input type="radio" name="is_active" value="1" <?= $selectedActive === '1' ? 'checked' : '' ?>>
              Aktif
            </label>
            <label class="option-card">
              <input type="radio" name="is_active" value="0" <?= $selectedActive === '0' ? 'checked' : '' ?>>
              Nonaktif
            </label>
          </div>
        </div>
      </div>
    </div>

    <div class="flex gap-3">
      <button type="submit" class="panel-button flex-1 justify-center">
        <?= $isEdit ? 'Simpan Perubahan' : 'Simpan Anggota' ?>
      </button>
      <a href="<?= site_url('members') ?>" class="panel-button-secondary flex-1 justify-center">Batal</a>
    </div>
  </form>

  <div class="space-y-6">
    <div class="panel-card p-6">
      <h2 class="section-heading mb-4">Ringkasan</h2>
      <div class="space-y-3 text-sm">
        <div class="flex items-center justify-between">
          <span class="text-slate-500">Inisial</span>
          <span class="font-medium"><?= esc(person_initials($member['full_name'] ?? '')) ?></span>
        </div>
        <div class="flex items-center justify-between">
          <span class="text-slate-500">Status</span>
          <span class="font-medium"><?= ((int) ($member['is_active'] ?? 1) === 1) ? 'Aktif' : 'Nonaktif' ?></span>
        </div>
        <?php if ($isEdit): ?>
          <div class="flex items-center justify-between">
            <span class="text-slate-500">Riwayat Transaksi</span>
            <span class="font-medium"><?= esc((string) $historyPagination['total_rows']) ?></span>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($isEdit): ?>
      <form method="post" action="<?= site_url('members/' . $memberId . '/delete') ?>" class="panel-card p-6" onsubmit="return confirm('Hapus anggota ini?');">
        <h2 class="text-lg font-semibold text-destructive">Hapus Anggota</h2>
        <p class="mt-2 text-sm text-slate-500">Hanya bisa dilakukan bila anggota ini belum pernah memiliki transaksi.</p>
        <button type="submit" class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-destructive px-4 py-2 text-sm font-medium text-white hover:opacity-90">
          Hapus Anggota
        </button>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php if ($isEdit): ?>
  <div class="panel-card p-6">
    <div class="mb-4">
      <h2 class="section-heading">Riwayat Peminjaman</h2>
      <p class="section-description">Daftar peminjaman yang pernah dilakukan anggota ini.</p>
    </div>

    <?php if ($history !== []): ?>
      <div class="mb-4 flex flex-col gap-2 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between">
        <p>Menampilkan <?= esc((string) $historyPagination['from']) ?>-<?= esc((string) $historyPagination['to']) ?> dari <?= esc((string) $historyPagination['total_rows']) ?> transaksi anggota ini.</p>
      </div>
    <?php endif; ?>

    <div class="data-table-wrapper overflow-x-auto">
      <table class="data-table">
        <thead>
          <tr>
            <th>Buku</th>
            <th>Kode Copy</th>
            <th>Pinjam</th>
            <th>Jatuh Tempo</th>
            <th>Kembali</th>
            <th>Status</th>
            <th>Denda</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($history === []): ?>
            <tr>
              <td colspan="7" class="py-10 text-center text-sm text-slate-500">Belum ada riwayat peminjaman.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($history as $item): ?>
              <tr>
                <td class="font-medium"><?= esc($item['book_title']) ?></td>
                <td class="text-slate-500"><?= esc($item['copy_code']) ?></td>
                <td class="text-slate-500"><?= esc(format_indo_date($item['borrowed_at'])) ?></td>
                <td class="text-slate-500"><?= esc(format_indo_date($item['due_at'])) ?></td>
                <td class="text-slate-500"><?= esc($item['returned_at'] ? format_indo_date($item['returned_at']) : '-') ?></td>
                <td>
                  <span class="status-badge <?= $item['status'] === 'lost' ? 'status-badge-overdue' : 'status-badge-' . esc($item['status']) ?>">
                    <?= esc(loan_status_label($item['status'])) ?>
                  </span>
                  <?php if (! empty($item['return_condition']) && $item['status'] !== 'lost'): ?>
                    <p class="mt-1 text-xs text-slate-500"><?= esc(loan_condition_label($item['return_condition'])) ?></p>
                  <?php endif; ?>
                </td>
                <td class="text-slate-500">
                  <?php if ((int) ($item['open_replacement_count'] ?? 0) > 0): ?>
                    <span class="status-badge status-badge-neutral">Menunggu Penggantian</span>
                  <?php elseif (isset($item['fine_amount']) && (float) $item['fine_amount'] > 0): ?>
                    <?= esc(rupiah($item['fine_amount'])) ?>
                  <?php else: ?>
                    -
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if (($historyPagination['total_pages'] ?? 1) > 1): ?>
      <div class="mt-4 content-toolbar">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <p class="text-sm text-slate-500">Halaman <?= esc((string) $historyPagination['page']) ?> dari <?= esc((string) $historyPagination['total_pages']) ?></p>
          <div class="flex flex-wrap gap-2">
            <?php
            $prevPage = max(1, (int) $historyPagination['page'] - 1);
            $nextPage = min((int) $historyPagination['total_pages'], (int) $historyPagination['page'] + 1);
            $startPage = max(1, (int) $historyPagination['page'] - 2);
            $endPage = min((int) $historyPagination['total_pages'], $startPage + 4);
            $startPage = max(1, $endPage - 4);
            ?>

            <a href="<?= site_url('members/' . $memberId . '/edit' . ($historyPagination['page'] > 1 ? '?' . http_build_query(['history_page' => $prevPage]) : '')) ?>" class="panel-button-secondary <?= $historyPagination['page'] <= 1 ? 'pointer-events-none opacity-50' : '' ?>">Sebelumnya</a>

            <?php for ($pageNumber = $startPage; $pageNumber <= $endPage; $pageNumber++): ?>
              <?php $pageUrl = site_url('members/' . $memberId . '/edit' . '?' . http_build_query(['history_page' => $pageNumber])); ?>
              <a href="<?= $pageUrl ?>" class="<?= $pageNumber === (int) $historyPagination['page'] ? 'panel-button' : 'panel-button-secondary' ?>">
                <?= esc((string) $pageNumber) ?>
              </a>
            <?php endfor; ?>

            <a href="<?= site_url('members/' . $memberId . '/edit' . '?' . http_build_query(['history_page' => $historyPagination['page'] < $historyPagination['total_pages'] ? $nextPage : $historyPagination['page']])) ?>" class="panel-button-secondary <?= $historyPagination['page'] >= $historyPagination['total_pages'] ? 'pointer-events-none opacity-50' : '' ?>">Berikutnya</a>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('pageSkeleton') ?>
<?= $this->include('partials/skeletons/members') ?>
<?= $this->endSection() ?>
