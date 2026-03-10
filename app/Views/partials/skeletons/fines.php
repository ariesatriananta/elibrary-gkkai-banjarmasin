<div class="page-loading-skeleton-panel">
  <div class="page-loading-skeleton-inner space-y-4">
    <div class="page-skeleton-line h-7 w-40"></div>
    <div class="page-skeleton-line h-4 w-80 max-w-full"></div>
  </div>
</div>
<div class="grid grid-cols-1 gap-6 xl:grid-cols-[0.8fr_1.2fr]">
  <div class="page-loading-skeleton-panel">
    <div class="page-loading-skeleton-inner space-y-4">
      <div class="page-skeleton-line h-5 w-36"></div>
      <?php for ($i = 0; $i < 3; $i++): ?>
        <div class="page-skeleton-block h-12 rounded-2xl"></div>
      <?php endfor; ?>
    </div>
  </div>
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    <?php for ($i = 0; $i < 3; $i++): ?>
      <div class="page-loading-skeleton-panel page-skeleton-stat"></div>
    <?php endfor; ?>
  </div>
</div>
<?php for ($panel = 0; $panel < 2; $panel++): ?>
  <div class="page-loading-skeleton-panel">
    <div class="page-loading-skeleton-inner space-y-4">
      <div class="page-skeleton-line h-6 w-52"></div>
      <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <?php for ($i = 0; $i < 4; $i++): ?>
          <div class="page-skeleton-block h-20 rounded-2xl"></div>
        <?php endfor; ?>
      </div>
      <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        <div class="page-skeleton-block h-44 rounded-[1.5rem]"></div>
        <div class="page-skeleton-block h-44 rounded-[1.5rem]"></div>
      </div>
    </div>
  </div>
<?php endfor; ?>
