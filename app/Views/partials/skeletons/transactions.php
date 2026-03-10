<div class="page-loading-skeleton-panel">
  <div class="page-loading-skeleton-inner space-y-4">
    <div class="page-skeleton-line h-7 w-72 max-w-full"></div>
    <div class="page-skeleton-line h-4 w-80 max-w-full"></div>
    <div class="flex gap-2">
      <div class="page-skeleton-block h-11 w-32 rounded-2xl"></div>
      <div class="page-skeleton-block h-11 w-36 rounded-2xl"></div>
      <div class="page-skeleton-block h-11 w-24 rounded-2xl"></div>
    </div>
  </div>
</div>
<div class="page-loading-skeleton-panel">
  <div class="page-loading-skeleton-inner space-y-4">
    <div class="page-skeleton-line h-5 w-44"></div>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
      <?php for ($i = 0; $i < 4; $i++): ?>
        <div class="page-skeleton-block h-12 rounded-2xl"></div>
      <?php endfor; ?>
      <div class="page-skeleton-block h-28 rounded-2xl md:col-span-2"></div>
      <div class="page-skeleton-block h-11 w-48 rounded-2xl md:col-span-2"></div>
    </div>
  </div>
</div>
<div class="page-loading-skeleton-panel">
  <div class="page-loading-skeleton-inner space-y-4">
    <div class="grid grid-cols-1 gap-3 lg:grid-cols-[1.3fr_0.7fr_auto]">
      <div class="page-skeleton-block h-11 rounded-2xl"></div>
      <div class="page-skeleton-block h-11 rounded-2xl"></div>
      <div class="page-skeleton-block h-11 rounded-2xl"></div>
    </div>
    <?php for ($row = 0; $row < 5; $row++): ?>
      <div class="page-skeleton-table-row page-skeleton-block"></div>
    <?php endfor; ?>
  </div>
</div>
