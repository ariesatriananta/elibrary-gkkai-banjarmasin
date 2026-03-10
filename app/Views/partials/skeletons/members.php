<div class="page-loading-skeleton-panel">
  <div class="page-loading-skeleton-inner flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div class="space-y-3">
      <div class="page-skeleton-line h-7 w-44"></div>
      <div class="page-skeleton-line h-4 w-72 max-w-full"></div>
    </div>
    <div class="page-skeleton-block h-11 w-44 rounded-2xl"></div>
  </div>
</div>
<div class="page-skeleton-grid">
  <?php for ($i = 0; $i < 4; $i++): ?>
    <div class="page-loading-skeleton-panel page-skeleton-stat"></div>
  <?php endfor; ?>
</div>
<div class="page-loading-skeleton-panel">
  <div class="page-loading-skeleton-inner space-y-4">
    <div class="grid grid-cols-1 gap-3 lg:grid-cols-[1.4fr_0.8fr_auto]">
      <div class="page-skeleton-block h-11 rounded-2xl"></div>
      <div class="page-skeleton-block h-11 rounded-2xl"></div>
      <div class="page-skeleton-block h-11 rounded-2xl"></div>
    </div>
    <?php for ($row = 0; $row < 6; $row++): ?>
      <div class="page-skeleton-table-row page-skeleton-block"></div>
    <?php endfor; ?>
  </div>
</div>
