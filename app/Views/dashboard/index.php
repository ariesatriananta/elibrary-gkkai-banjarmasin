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
    <div class="table-divider p-4">
      <h3 class="text-base font-semibold">Transaksi Terbaru</h3>
    </div>
    <div class="space-y-1 p-4">
      <?php if ($recentTransactions === []): ?>
        <p class="text-sm text-slate-500">Belum ada transaksi terbaru.</p>
      <?php else: ?>
        <?php foreach ($recentTransactions as $item): ?>
          <div class="table-divider flex items-center justify-between py-2 last:border-b-0">
            <div>
              <p class="text-sm font-medium"><?= esc($item['book_title']) ?></p>
              <p class="text-xs text-slate-500"><?= esc($item['member_name']) ?> - <?= esc(format_indo_date($item['borrowed_at'])) ?></p>
            </div>
            <span class="status-badge status-badge-<?= esc($item['status']) ?>">
              <?= esc(loan_status_label($item['status'])) ?>
            </span>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <div class="panel-card">
    <div class="table-divider p-4">
      <h3 class="text-base font-semibold">Ringkasan Keterlambatan</h3>
    </div>
    <div class="p-4">
      <div class="mb-4 grid grid-cols-2 gap-4">
        <div class="rounded-[1.25rem] border border-white/55 bg-white/50 p-4 text-center backdrop-blur-lg">
          <p class="text-2xl font-bold text-destructive"><?= esc(rupiah($fineSummary['unpaid'])) ?></p>
          <p class="mt-1 text-xs text-slate-500">Denda Belum Lunas</p>
        </div>
        <div class="rounded-[1.25rem] border border-white/55 bg-white/50 p-4 text-center backdrop-blur-lg">
          <p class="text-2xl font-bold text-success"><?= esc(rupiah($fineSummary['collected'])) ?></p>
          <p class="mt-1 text-xs text-slate-500">Denda Terkumpul</p>
        </div>
      </div>

      <div class="space-y-1">
        <?php if ($lateSummaries === []): ?>
          <p class="text-sm text-slate-500">Tidak ada keterlambatan saat ini.</p>
        <?php else: ?>
          <?php foreach ($lateSummaries as $late): ?>
            <div class="table-divider flex items-center justify-between py-2 last:border-b-0">
              <div>
                <p class="text-sm font-medium"><?= esc($late['member_name']) ?></p>
                <p class="text-xs text-slate-500"><?= esc($late['book_title']) ?> - <?= esc((string) $late['late_days']) ?> hari terlambat</p>
              </div>
              <span class="text-sm font-semibold text-destructive"><?= esc(rupiah($late['amount'])) ?></span>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('pageSkeleton') ?>
<?= $this->include('partials/skeletons/dashboard') ?>
<?= $this->endSection() ?>
