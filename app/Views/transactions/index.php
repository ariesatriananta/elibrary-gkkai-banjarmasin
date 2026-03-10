<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div>
  <h1 class="text-2xl font-bold tracking-tight">Peminjaman & Pengembalian</h1>
  <p class="text-slate-500">Catat peminjaman buku, pengembalian, dan histori transaksi.</p>
</div>

<div class="inline-flex w-fit gap-1 rounded-lg bg-muted p-1">
  <button class="transaction-tab rounded-lg px-4 py-2 text-sm font-medium text-slate-500" data-tab="borrow" type="button">Pinjam Buku</button>
  <button class="transaction-tab rounded-lg px-4 py-2 text-sm font-medium text-slate-500" data-tab="return" type="button">Kembalikan Buku</button>
  <button class="transaction-tab rounded-lg bg-primary/10 px-4 py-2 text-sm font-medium text-primary" data-tab="history" type="button">Riwayat</button>
</div>

<div class="transaction-panel hidden" data-panel="borrow">
  <div class="panel-card p-6">
    <h3 class="mb-4 text-base font-semibold">Form Peminjaman</h3>
    <div class="grid max-w-2xl grid-cols-1 gap-4 md:grid-cols-2">
      <div>
        <label class="mb-1 block text-sm font-medium">Anggota</label>
        <select class="panel-input">
          <option>Pilih anggota</option>
          <?php foreach ($members as $member): ?>
            <option><?= esc($member) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium">Buku</label>
        <select class="panel-input">
          <option>Pilih buku</option>
          <?php foreach ($books as $book): ?>
            <option><?= esc($book) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium">Tanggal Pinjam</label>
        <input type="date" value="2025-02-25" class="panel-input">
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium">Tanggal Jatuh Tempo</label>
        <input type="date" value="2025-03-11" class="panel-input">
      </div>
      <div class="md:col-span-2">
        <button class="panel-button" type="button">Simpan Peminjaman</button>
      </div>
    </div>
  </div>
</div>

<div class="transaction-panel hidden" data-panel="return">
  <div class="panel-card p-6">
    <h3 class="mb-4 text-base font-semibold">Form Pengembalian</h3>
    <div class="grid max-w-2xl grid-cols-1 gap-4 md:grid-cols-2">
      <div>
        <label class="mb-1 block text-sm font-medium">Pinjaman Aktif</label>
        <select class="panel-input">
          <option>Pilih pinjaman aktif</option>
          <?php foreach ($activeLoans as $loan): ?>
            <option><?= esc($loan) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="mb-1 block text-sm font-medium">Tanggal Kembali</label>
        <input type="date" value="2025-02-25" class="panel-input">
      </div>
      <div class="md:col-span-2">
        <button class="panel-button-secondary" type="button">Simpan Pengembalian</button>
      </div>
    </div>
  </div>
</div>

<div class="transaction-panel" data-panel="history">
  <div class="relative mb-4 max-w-md">
    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <circle cx="11" cy="11" r="8"></circle>
      <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
    </svg>
    <input type="text" placeholder="Cari transaksi..." class="panel-input pl-9">
  </div>

  <div class="panel-card overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full border-collapse">
        <thead>
          <tr class="text-left text-xs font-medium text-slate-500">
            <th class="border-b border-border px-4 py-3">Buku</th>
            <th class="border-b border-border px-4 py-3">Anggota</th>
            <th class="border-b border-border px-4 py-3">Pinjam</th>
            <th class="border-b border-border px-4 py-3">Jatuh Tempo</th>
            <th class="border-b border-border px-4 py-3">Kembali</th>
            <th class="border-b border-border px-4 py-3">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($history as $row): ?>
            <tr class="text-sm">
              <td class="border-b border-border px-4 py-3 font-medium"><?= esc($row['book']) ?></td>
              <td class="border-b border-border px-4 py-3"><?= esc($row['member']) ?></td>
              <td class="border-b border-border px-4 py-3 text-slate-500"><?= esc($row['borrowed']) ?></td>
              <td class="border-b border-border px-4 py-3 text-slate-500"><?= esc($row['due']) ?></td>
              <td class="border-b border-border px-4 py-3 text-slate-500"><?= esc($row['returned']) ?></td>
              <td class="border-b border-border px-4 py-3">
                <span class="status-badge status-badge-<?= esc($row['status']) ?>">
                  <?= esc($row['status']) ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  const tabs = document.querySelectorAll('.transaction-tab');
  const panels = document.querySelectorAll('.transaction-panel');

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      const target = tab.dataset.tab;

      tabs.forEach((item) => {
        item.classList.remove('bg-primary/10', 'text-primary');
        item.classList.add('text-slate-500');
      });

      panels.forEach((panel) => {
        panel.classList.add('hidden');
      });

      tab.classList.add('bg-primary/10', 'text-primary');
      tab.classList.remove('text-slate-500');
      document.querySelector(`[data-panel="${target}"]`).classList.remove('hidden');
    });
  });
</script>
<?= $this->endSection() ?>
