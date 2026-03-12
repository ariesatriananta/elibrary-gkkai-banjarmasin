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
<div class="books-card-grid">
  <?php for ($i = 0; $i < 10; $i++): ?>
    <div class="page-loading-skeleton-panel overflow-hidden">
      <div class="page-skeleton-block h-36 rounded-none"></div>
      <div class="page-loading-skeleton-inner space-y-4">
        <div class="space-y-2">
          <div class="page-skeleton-line h-5 w-3/4"></div>
          <div class="page-skeleton-line h-4 w-1/2"></div>
        </div>
        <div class="grid grid-cols-3 gap-3">
          <div class="page-skeleton-block h-14 rounded-2xl"></div>
          <div class="page-skeleton-block h-14 rounded-2xl"></div>
          <div class="page-skeleton-block h-14 rounded-2xl"></div>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div class="page-skeleton-line h-4 rounded-xl"></div>
          <div class="page-skeleton-line h-4 rounded-xl"></div>
        </div>
        <div class="flex justify-end gap-2">
          <div class="page-skeleton-block h-10 w-10 rounded-xl"></div>
          <div class="page-skeleton-block h-10 w-10 rounded-xl"></div>
        </div>
      </div>
    </div>
  <?php endfor; ?>
</div>
