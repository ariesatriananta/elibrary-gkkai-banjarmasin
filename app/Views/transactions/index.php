<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<?php
$errors = $errors ?? [];
$activeTab = $activeTab ?? 'history';
?>
<div class="page-header">
  <div>
    <h1 class="page-title">Peminjaman & Pengembalian</h1>
    <p class="page-description">Catat peminjaman, pengembalian, dan pantau histori transaksi.</p>
  </div>
</div>

<div class="inline-flex w-fit gap-1 rounded-lg bg-muted p-1">
  <button class="transaction-tab rounded-lg px-4 py-2 text-sm font-medium <?= $activeTab === 'borrow' ? 'bg-primary/10 text-primary' : 'text-slate-500' ?>" data-tab="borrow" type="button">Pinjam Buku</button>
  <button class="transaction-tab rounded-lg px-4 py-2 text-sm font-medium <?= $activeTab === 'return' ? 'bg-primary/10 text-primary' : 'text-slate-500' ?>" data-tab="return" type="button">Kembalikan Buku</button>
  <button class="transaction-tab rounded-lg px-4 py-2 text-sm font-medium <?= $activeTab === 'history' ? 'bg-primary/10 text-primary' : 'text-slate-500' ?>" data-tab="history" type="button">Riwayat</button>
</div>

<div class="transaction-panel <?= $activeTab === 'borrow' ? '' : 'hidden' ?>" data-panel="borrow">
  <div class="panel-card p-6">
    <h3 class="section-heading mb-4 text-base">Form Peminjaman</h3>
    <?php if ($members === [] || $availableCopies === []): ?>
      <div class="soft-info">
        <?= $members === [] ? 'Belum ada anggota aktif.' : 'Belum ada copy buku yang tersedia untuk dipinjam.' ?>
      </div>
    <?php else: ?>
      <form method="post" action="<?= site_url('transactions/borrow') ?>" class="grid max-w-3xl grid-cols-1 gap-4 md:grid-cols-2">
        <div>
          <label class="mb-1 block text-sm font-medium">Anggota</label>
          <select name="member_id" class="panel-input <?= field_error_class($errors, 'member_id') ?>" required>
            <option value="">Pilih anggota</option>
            <?php foreach ($members as $member): ?>
              <option value="<?= esc((string) $member['id']) ?>" <?= old('member_id') === (string) $member['id'] ? 'selected' : '' ?>>
                <?= esc($member['member_number'] . ' - ' . $member['full_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php if (field_error($errors, 'member_id')): ?>
            <p class="field-error mt-1"><?= esc(field_error($errors, 'member_id')) ?></p>
          <?php endif; ?>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium">Copy Buku</label>
          <select name="book_copy_id" class="panel-input <?= field_error_class($errors, 'book_copy_id') ?>" required>
            <option value="">Pilih copy buku</option>
            <?php foreach ($availableCopies as $copy): ?>
              <option value="<?= esc((string) $copy['id']) ?>" <?= old('book_copy_id') === (string) $copy['id'] ? 'selected' : '' ?>>
                <?= esc($copy['title'] . ' - ' . $copy['copy_code']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php if (field_error($errors, 'book_copy_id')): ?>
            <p class="field-error mt-1"><?= esc(field_error($errors, 'book_copy_id')) ?></p>
          <?php endif; ?>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium">Tanggal Pinjam</label>
          <input type="date" name="borrowed_at" value="<?= esc(old('borrowed_at', $defaultBorrowDate)) ?>" class="panel-input <?= field_error_class($errors, 'borrowed_at') ?>" required>
          <?php if (field_error($errors, 'borrowed_at')): ?>
            <p class="field-error mt-1"><?= esc(field_error($errors, 'borrowed_at')) ?></p>
          <?php endif; ?>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium">Tanggal Jatuh Tempo</label>
          <input type="date" name="due_at" value="<?= esc(old('due_at', $defaultDueDate)) ?>" class="panel-input <?= field_error_class($errors, 'due_at') ?>" required>
          <?php if (field_error($errors, 'due_at')): ?>
            <p class="field-error mt-1"><?= esc(field_error($errors, 'due_at')) ?></p>
          <?php endif; ?>
        </div>
        <div class="md:col-span-2">
          <label class="mb-1 block text-sm font-medium">Catatan</label>
          <textarea name="notes" rows="3" class="panel-input <?= field_error_class($errors, 'notes') ?>"><?= esc(old('notes', '')) ?></textarea>
          <?php if (field_error($errors, 'notes')): ?>
            <p class="field-error mt-1"><?= esc(field_error($errors, 'notes')) ?></p>
          <?php endif; ?>
        </div>
        <div class="md:col-span-2">
          <button class="panel-button" type="submit">Simpan Peminjaman</button>
        </div>
      </form>
    <?php endif; ?>
  </div>
</div>

<div class="transaction-panel <?= $activeTab === 'return' ? '' : 'hidden' ?>" data-panel="return">
  <div class="panel-card p-6">
    <h3 class="section-heading mb-4 text-base">Form Pengembalian</h3>
    <?php if ($activeLoans === []): ?>
      <div class="soft-info">Belum ada pinjaman aktif.</div>
    <?php else: ?>
      <form method="post" action="<?= site_url('transactions/return') ?>" class="grid max-w-3xl grid-cols-1 gap-4 md:grid-cols-2">
        <div>
          <label class="mb-1 block text-sm font-medium">Pinjaman Aktif</label>
          <select name="loan_id" class="panel-input <?= field_error_class($errors, 'loan_id') ?>" required>
            <option value="">Pilih pinjaman aktif</option>
            <?php foreach ($activeLoans as $loan): ?>
              <option value="<?= esc((string) $loan['id']) ?>" <?= old('loan_id') === (string) $loan['id'] ? 'selected' : '' ?>>
                <?= esc($loan['book_title'] . ' - ' . $loan['copy_code'] . ' - ' . $loan['member_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php if (field_error($errors, 'loan_id')): ?>
            <p class="field-error mt-1"><?= esc(field_error($errors, 'loan_id')) ?></p>
          <?php endif; ?>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium">Tanggal Kembali</label>
          <input type="date" name="returned_at" value="<?= esc(old('returned_at', date('Y-m-d'))) ?>" class="panel-input <?= field_error_class($errors, 'returned_at') ?>" required>
          <?php if (field_error($errors, 'returned_at')): ?>
            <p class="field-error mt-1"><?= esc(field_error($errors, 'returned_at')) ?></p>
          <?php endif; ?>
        </div>
        <div class="md:col-span-2">
          <label class="mb-1 block text-sm font-medium">Catatan</label>
          <textarea name="notes" rows="3" class="panel-input <?= field_error_class($errors, 'notes') ?>"><?= esc(old('notes', '')) ?></textarea>
          <?php if (field_error($errors, 'notes')): ?>
            <p class="field-error mt-1"><?= esc(field_error($errors, 'notes')) ?></p>
          <?php endif; ?>
        </div>
        <div class="md:col-span-2">
          <button class="panel-button-secondary" type="submit">Simpan Pengembalian</button>
        </div>
      </form>
    <?php endif; ?>
  </div>
</div>

<div class="transaction-panel <?= $activeTab === 'history' ? '' : 'hidden' ?>" data-panel="history">
  <form method="get" action="<?= site_url('transactions') ?>" class="content-toolbar mb-4 grid grid-cols-1 gap-3 lg:grid-cols-[1.3fr_0.7fr_auto]">
    <div class="relative">
      <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
      <input type="text" name="q" value="<?= esc($filters['q']) ?>" placeholder="Cari buku, copy, anggota..." class="panel-input pl-9">
    </div>
    <select name="status" class="panel-input">
      <option value="">Semua Status</option>
      <option value="borrowed" <?= $filters['status'] === 'borrowed' ? 'selected' : '' ?>>Dipinjam</option>
      <option value="overdue" <?= $filters['status'] === 'overdue' ? 'selected' : '' ?>>Terlambat</option>
      <option value="returned" <?= $filters['status'] === 'returned' ? 'selected' : '' ?>>Dikembalikan</option>
    </select>
    <div class="flex gap-2">
      <button type="submit" class="panel-button justify-center">Filter</button>
      <a href="<?= site_url('transactions') ?>" class="panel-button-secondary justify-center">Reset</a>
    </div>
  </form>

  <div class="data-table-wrapper">
    <div class="overflow-x-auto">
      <table class="data-table">
        <thead>
          <tr>
            <th>Buku</th>
            <th>Anggota</th>
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
              <td colspan="7" class="py-10 text-center text-sm text-slate-500">Belum ada transaksi.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($history as $row): ?>
              <tr>
                <td>
                  <p class="font-medium"><?= esc($row['book_title']) ?></p>
                  <p class="text-xs text-slate-500"><?= esc($row['copy_code']) ?></p>
                </td>
                <td>
                  <p><?= esc($row['member_name']) ?></p>
                  <p class="text-xs text-slate-500"><?= esc($row['member_number']) ?></p>
                </td>
                <td class="text-slate-500"><?= esc(format_indo_date($row['borrowed_at'])) ?></td>
                <td class="text-slate-500"><?= esc(format_indo_date($row['due_at'])) ?></td>
                <td class="text-slate-500"><?= esc($row['returned_at'] ? format_indo_date($row['returned_at']) : '-') ?></td>
                <td>
                  <span class="status-badge status-badge-<?= esc($row['status']) ?>">
                    <?= esc(loan_status_label($row['status'])) ?>
                  </span>
                </td>
                <td class="text-slate-500">
                  <?= isset($row['fine_amount']) && $row['fine_amount'] !== null ? esc(rupiah($row['fine_amount'])) : '-' ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageSkeleton') ?>
<?= $this->include('partials/skeletons/transactions') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  const tabs = document.querySelectorAll('.transaction-tab');
  const panels = document.querySelectorAll('.transaction-panel');
  const initialTab = <?= json_encode($activeTab) ?>;

  function openTransactionTab(target) {
    tabs.forEach((item) => {
      item.classList.remove('bg-primary/10', 'text-primary');
      item.classList.add('text-slate-500');
    });

    panels.forEach((panel) => {
      panel.classList.add('hidden');
    });

    const targetTab = document.querySelector(`[data-tab="${target}"]`);
    const targetPanel = document.querySelector(`[data-panel="${target}"]`);

    if (! targetTab || ! targetPanel) {
      return;
    }

    targetTab.classList.add('bg-primary/10', 'text-primary');
    targetTab.classList.remove('text-slate-500');
    targetPanel.classList.remove('hidden');
  }

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      openTransactionTab(tab.dataset.tab);
    });
  });

  openTransactionTab(initialTab);
</script>
<?= $this->endSection() ?>
