<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="page-header">
  <div>
    <h1 class="page-title">Dashboard</h1>
    <p class="page-description">Ringkasan singkat aktivitas perpustakaan.</p>
  </div>
</div>

<div class="dashboard-kpi-grid">
  <?php foreach ($stats as $stat): ?>
    <div class="stat-card">
      <p class="stat-card-label"><?= esc($stat['label']) ?></p>
      <p class="stat-card-value"><?= esc($stat['value']) ?></p>
    </div>
  <?php endforeach; ?>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
  <div class="panel-card">
    <div class="table-divider p-4">
      <h3 class="section-heading text-base">Transaksi Terbaru</h3>
    </div>
    <div class="space-y-1 p-4">
      <?php if ($recentTransactions === []): ?>
        <div class="soft-info">Belum ada transaksi terbaru.</div>
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
      <h3 class="section-heading text-base">Ringkasan Keterlambatan</h3>
    </div>
    <div class="p-4">
      <div class="mb-4 grid grid-cols-2 gap-4">
        <div class="metric-tile text-center">
          <p class="text-2xl font-bold text-destructive"><?= esc(rupiah($fineSummary['unpaid'])) ?></p>
          <p class="mt-1 text-xs text-slate-500">Denda Belum Lunas</p>
        </div>
        <div class="metric-tile text-center">
          <p class="text-2xl font-bold text-success"><?= esc(rupiah($fineSummary['collected'])) ?></p>
          <p class="mt-1 text-xs text-slate-500">Denda Terkumpul</p>
        </div>
      </div>

      <div class="space-y-1">
        <?php if ($lateSummaries === []): ?>
          <div class="soft-info">Tidak ada keterlambatan saat ini.</div>
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
