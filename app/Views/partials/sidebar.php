<?php $activeMenu = $activeMenu ?? ''; ?>
<aside id="app-sidebar" class="glass-sidebar app-fixed-sidebar app-sidebar-frame app-mobile-sidebar flex w-72 flex-shrink-0 flex-col">
  <div class="table-divider p-4">
    <div class="mb-3 flex items-center justify-between lg:hidden">
      <p class="text-sm font-semibold tracking-tight text-slate-600">Navigasi</p>
      <button type="button" id="mobile-sidebar-close" class="mobile-nav-toggle" aria-label="Tutup navigasi">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
          <line x1="6" y1="6" x2="18" y2="18"></line>
          <line x1="18" y1="6" x2="6" y2="18"></line>
        </svg>
      </button>
    </div>
    <div class="flex justify-center">
      <img src="<?= library_logo_url() ?>" alt="<?= esc(library_brand_name()) ?>" class="h-28 w-28 rounded-xl border border-white/70 bg-white/75 object-contain p-1.5 shadow-sm">
    </div>
  </div>

  <nav class="flex-1 space-y-1.5 p-3">
    <p class="px-3 py-2 text-xs font-medium text-slate-500">Navigasi</p>

    <a href="<?= site_url('/') ?>" class="sidebar-link <?= $activeMenu === 'dashboard' ? 'sidebar-link-active' : '' ?>" data-skeleton-template="dashboard">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <rect x="3" y="3" width="7" height="7"></rect>
        <rect x="14" y="3" width="7" height="7"></rect>
        <rect x="3" y="14" width="7" height="7"></rect>
        <rect x="14" y="14" width="7" height="7"></rect>
      </svg>
      Dashboard
    </a>

    <a href="<?= site_url('books') ?>" class="sidebar-link <?= $activeMenu === 'books' ? 'sidebar-link-active' : '' ?>" data-skeleton-template="books">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
      </svg>
      Data Buku
    </a>

    <a href="<?= site_url('members') ?>" class="sidebar-link <?= $activeMenu === 'members' ? 'sidebar-link-active' : '' ?>" data-skeleton-template="members">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
        <circle cx="9" cy="7" r="4"></circle>
      </svg>
      Data Anggota
    </a>

    <a href="<?= site_url('transactions') ?>" class="sidebar-link <?= $activeMenu === 'transactions' ? 'sidebar-link-active' : '' ?>" data-skeleton-template="transactions">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M8 3l4 4-4 4"></path>
        <path d="M16 21l-4-4 4-4"></path>
        <path d="M12 7h8"></path>
        <path d="M4 17h8"></path>
      </svg>
      Peminjaman
    </a>

    <a href="<?= site_url('fines') ?>" class="sidebar-link <?= $activeMenu === 'fines' ? 'sidebar-link-active' : '' ?>" data-skeleton-template="fines">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <line x1="12" y1="1" x2="12" y2="23"></line>
        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
      </svg>
      Denda & Bonus
    </a>
  </nav>

  <div class="table-divider mx-3"></div>
  <div class="sidebar-footer-note px-4 py-4 text-center text-slate-500">
    &copy; <?= esc(date('Y')) ?> <?= esc(library_brand_name()) ?><br>
    <?= esc(church_name()) ?>
  </div>
</aside>
