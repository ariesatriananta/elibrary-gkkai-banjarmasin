<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div>
  <h1 class="text-2xl font-bold tracking-tight">Denda & Bonus</h1>
  <p class="text-slate-500">Kelola keterlambatan, pembayaran denda, dan catatan bonus manual.</p>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
  <div class="panel-card p-5 text-center">
    <p class="text-sm text-slate-500">Total Denda</p>
    <p class="mt-1 text-2xl font-bold"><?= esc($summary['total']) ?></p>
  </div>
  <div class="panel-card p-5 text-center">
    <p class="text-sm text-slate-500">Belum Lunas</p>
    <p class="mt-1 text-2xl font-bold text-destructive"><?= esc($summary['unpaid']) ?></p>
  </div>
  <div class="panel-card p-5 text-center">
    <p class="text-sm text-slate-500">Terkumpul</p>
    <p class="mt-1 text-2xl font-bold text-success"><?= esc($summary['collected']) ?></p>
  </div>
</div>

<div class="panel-card overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full border-collapse">
      <thead>
        <tr class="text-left text-xs font-medium text-slate-500">
          <th class="border-b border-border px-4 py-3">Anggota</th>
          <th class="border-b border-border px-4 py-3">Buku</th>
          <th class="border-b border-border px-4 py-3">Hari Terlambat</th>
          <th class="border-b border-border px-4 py-3">Nominal</th>
          <th class="border-b border-border px-4 py-3">Tanggal</th>
          <th class="border-b border-border px-4 py-3">Status</th>
          <th class="border-b border-border px-4 py-3">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($fines as $fine): ?>
          <tr class="text-sm">
            <td class="border-b border-border px-4 py-3 font-medium"><?= esc($fine['member']) ?></td>
            <td class="border-b border-border px-4 py-3 text-slate-500"><?= esc($fine['book']) ?></td>
            <td class="border-b border-border px-4 py-3"><?= esc((string) $fine['days_late']) ?></td>
            <td class="border-b border-border px-4 py-3 font-semibold"><?= esc($fine['amount']) ?></td>
            <td class="border-b border-border px-4 py-3 text-slate-500"><?= esc($fine['date']) ?></td>
            <td class="border-b border-border px-4 py-3">
              <span class="status-badge <?= $fine['status'] === 'paid' ? 'status-badge-returned' : 'status-badge-overdue' ?>">
                <?= esc($fine['status'] === 'paid' ? 'Lunas' : 'Belum Lunas') ?>
              </span>
            </td>
            <td class="border-b border-border px-4 py-3">
              <?php if ($fine['status'] !== 'paid'): ?>
                <button class="rounded-lg border border-border px-3 py-1 text-xs hover:bg-muted" type="button">Tandai Lunas</button>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>
