<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="flex items-center justify-between gap-4">
  <div>
    <h1 class="text-2xl font-bold tracking-tight">Data Anggota</h1>
    <p class="text-slate-500">Kelola anggota perpustakaan dan status pinjamannya.</p>
  </div>
  <button class="panel-button" type="button">
    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <line x1="12" y1="5" x2="12" y2="19"></line>
      <line x1="5" y1="12" x2="19" y2="12"></line>
    </svg>
    Tambah Anggota
  </button>
</div>

<div class="relative max-w-md">
  <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
    <circle cx="11" cy="11" r="8"></circle>
    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
  </svg>
  <input type="text" placeholder="Cari anggota..." class="panel-input pl-9">
</div>

<div class="panel-card overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full border-collapse">
      <thead>
        <tr class="text-left text-xs font-medium text-slate-500">
          <th class="border-b border-border px-4 py-3">Nama</th>
          <th class="border-b border-border px-4 py-3">Telepon</th>
          <th class="border-b border-border px-4 py-3">Email</th>
          <th class="border-b border-border px-4 py-3">Bergabung</th>
          <th class="border-b border-border px-4 py-3">Pinjaman Aktif</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($members as $member): ?>
          <tr class="text-sm">
            <td class="border-b border-border px-4 py-3">
              <div class="flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-xs font-semibold text-primary">
                  <?= esc($member['initials']) ?>
                </div>
                <span class="font-medium"><?= esc($member['name']) ?></span>
              </div>
            </td>
            <td class="border-b border-border px-4 py-3 text-slate-500"><?= esc($member['phone']) ?></td>
            <td class="border-b border-border px-4 py-3 text-slate-500"><?= esc($member['email']) ?></td>
            <td class="border-b border-border px-4 py-3 text-slate-500"><?= esc($member['joined']) ?></td>
            <td class="border-b border-border px-4 py-3">
              <span class="status-badge <?= $member['active_loans'] > 0 ? 'status-badge-borrowed' : 'status-badge-returned' ?>">
                <?= esc((string) $member['active_loans']) ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>
