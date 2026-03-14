<div class="page-loading-skeleton-panel">
  <div class="page-loading-skeleton-inner flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div class="space-y-3">
      <div class="page-skeleton-line h-7 w-40"></div>
      <div class="page-skeleton-line h-4 w-80 max-w-full"></div>
    </div>
    <div class="page-skeleton-block h-11 w-40 rounded-2xl"></div>
  </div>
</div>
<div class="page-skeleton-grid">
  <?php for ($i = 0; $i < 4; $i++): ?>
    <div class="page-loading-skeleton-panel page-skeleton-stat"></div>
  <?php endfor; ?>
</div>
<div class="page-loading-skeleton-panel">
  <div class="page-loading-skeleton-inner space-y-4">
    <div class="grid grid-cols-1 gap-3 lg:grid-cols-[1.5fr_0.8fr_0.8fr_0.7fr_auto]">
      <div class="page-skeleton-block h-11 rounded-2xl"></div>
      <div class="page-skeleton-block h-11 rounded-2xl"></div>
      <div class="page-skeleton-block h-11 rounded-2xl"></div>
      <div class="page-skeleton-block h-11 rounded-2xl"></div>
      <div class="page-skeleton-block h-11 rounded-2xl"></div>
    </div>
  </div>
</div>
<div class="page-loading-skeleton-panel">
  <div class="page-loading-skeleton-inner space-y-4">
    <div class="grid grid-cols-[1.4fr_0.8fr_0.8fr_0.8fr_0.6fr_0.8fr_0.5fr] gap-3">
      <?php for ($i = 0; $i < 7; $i++): ?>
        <div class="page-skeleton-block h-11 rounded-2xl"></div>
      <?php endfor; ?>
    </div>
    <?php for ($i = 0; $i < 6; $i++): ?>
      <div class="grid grid-cols-[1.4fr_0.8fr_0.8fr_0.8fr_0.6fr_0.8fr_0.5fr] gap-3">
        <div class="page-skeleton-block h-16 rounded-2xl"></div>
        <div class="page-skeleton-block h-16 rounded-2xl"></div>
        <div class="page-skeleton-block h-16 rounded-2xl"></div>
        <div class="page-skeleton-block h-16 rounded-2xl"></div>
        <div class="page-skeleton-block h-16 rounded-2xl"></div>
        <div class="page-skeleton-block h-16 rounded-2xl"></div>
        <div class="page-skeleton-block h-16 rounded-2xl"></div>
      </div>
    <?php endfor; ?>
  </div>
</div>
