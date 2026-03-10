<div class="page-loading-skeleton-panel">
  <div class="page-loading-skeleton-inner space-y-4">
    <div class="page-skeleton-line h-7 w-64"></div>
    <div class="page-skeleton-line h-4 w-80 max-w-full"></div>
  </div>
</div>
<div class="page-skeleton-grid">
  <?php for ($i = 0; $i < 4; $i++): ?>
    <div class="page-loading-skeleton-panel page-skeleton-stat"></div>
  <?php endfor; ?>
</div>
<div class="page-loading-skeleton-panel">
  <div class="page-loading-skeleton-inner space-y-4">
    <div class="page-skeleton-line h-5 w-48"></div>
    <div class="page-skeleton-block h-14 rounded-2xl"></div>
    <?php for ($i = 0; $i < 4; $i++): ?>
      <div class="page-skeleton-table-row page-skeleton-block"></div>
    <?php endfor; ?>
  </div>
</div>
