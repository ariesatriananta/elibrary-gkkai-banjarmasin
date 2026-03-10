<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div>
  <h1 class="text-2xl font-bold tracking-tight">Dashboard</h1>
  <p class="text-slate-500">Ringkasan singkat aktivitas perpustakaan.</p>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
  <?php foreach ($stats as $stat): ?>
    <div class="panel-card p-5">
      <p class="text-sm text-slate-500"><?= esc($stat['label']) ?></p>
      <p class="mt-1 text-3xl font-bold"><?= esc($stat['value']) ?></p>
    </div>
  <?php endforeach; ?>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
  <div class="panel-card">
    <div class="border-b border-border p-4">
      <h3 class="text-base font-semibold">Transaksi Terbaru</h3>
    </div>
    <div class="space-y-1 p-4">
      <?php foreach ($recentTransactions as $item): ?>
        <div class="flex items-center justify-between border-b border-border py-2 last:border-b-0">
          <div>
            <p class="text-sm font-medium"><?= esc($item['book']) ?></p>
            <p class="text-xs text-slate-500"><?= esc($item['member']) ?> · <?= esc($item['date']) ?></p>
          </div>
          <span class="status-badge status-badge-<?= esc($item['status']) ?>">
            <?= esc($item['status']) ?>
          </span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="panel-card">
    <div class="border-b border-border p-4">
      <h3 class="text-base font-semibold">Ringkasan Keterlambatan</h3>
    </div>
    <div class="p-4">
      <div class="mb-4 grid grid-cols-2 gap-4">
        <div class="rounded-xl bg-destructive/10 p-4 text-center">
          <p class="text-2xl font-bold text-destructive"><?= esc($fineSummary['unpaid']) ?></p>
          <p class="mt-1 text-xs text-slate-500">Denda Belum Lunas</p>
        </div>
        <div class="rounded-xl bg-success/10 p-4 text-center">
          <p class="text-2xl font-bold text-success"><?= esc($fineSummary['collected']) ?></p>
          <p class="mt-1 text-xs text-slate-500">Denda Terkumpul</p>
        </div>
      </div>

      <div class="space-y-1">
        <?php foreach ($lateSummaries as $late): ?>
          <div class="flex items-center justify-between border-b border-border py-2 last:border-b-0">
            <div>
              <p class="text-sm font-medium"><?= esc($late['member']) ?></p>
              <p class="text-xs text-slate-500"><?= esc($late['book']) ?> · <?= esc($late['days_late']) ?> hari terlambat</p>
            </div>
            <span class="text-sm font-semibold text-destructive"><?= esc($late['amount']) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
