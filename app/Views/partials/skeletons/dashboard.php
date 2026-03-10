<div class="page-loading-skeleton-panel">
  <div class="page-loading-skeleton-inner space-y-4">
    <div class="page-skeleton-line h-7 w-48"></div>
    <div class="page-skeleton-line h-4 w-72 max-w-full"></div>
  </div>
</div>
<div class="page-skeleton-grid">
  <?php for ($i = 0; $i < 4; $i++): ?>
    <div class="page-loading-skeleton-panel page-skeleton-stat"></div>
  <?php endfor; ?>
</div>
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
  <?php for ($panel = 0; $panel < 2; $panel++): ?>
    <div class="page-loading-skeleton-panel">
      <div class="page-loading-skeleton-inner space-y-4">
        <div class="page-skeleton-line h-5 w-40"></div>
        <?php for ($row = 0; $row < 4; $row++): ?>
          <div class="page-skeleton-table-row page-skeleton-block"></div>
        <?php endfor; ?>
      </div>
    </div>
  <?php endfor; ?>
</div>
