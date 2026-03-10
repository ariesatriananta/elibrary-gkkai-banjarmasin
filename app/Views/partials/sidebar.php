<?php $activeMenu = $activeMenu ?? ''; ?>
<aside class="w-64 flex-shrink-0 border-r border-border bg-white">
  <div class="flex items-center gap-2 border-b border-border p-4">
    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary">
      <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
      </svg>
    </div>
    <div class="leading-tight">
      <p class="text-sm font-semibold">Elibrary GKKAI</p>
      <p class="text-xs text-slate-500">Sistem Perpustakaan</p>
    </div>
  </div>

  <nav class="space-y-1 p-3">
    <p class="px-3 py-2 text-xs font-medium text-slate-500">Navigasi</p>

    <a href="<?= site_url('/') ?>" class="sidebar-link <?= $activeMenu === 'dashboard' ? 'sidebar-link-active' : '' ?>">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <rect x="3" y="3" width="7" height="7"></rect>
        <rect x="14" y="3" width="7" height="7"></rect>
        <rect x="3" y="14" width="7" height="7"></rect>
        <rect x="14" y="14" width="7" height="7"></rect>
      </svg>
      Dashboard
    </a>

    <a href="<?= site_url('books') ?>" class="sidebar-link <?= $activeMenu === 'books' ? 'sidebar-link-active' : '' ?>">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
      </svg>
      Data Buku
    </a>

    <a href="<?= site_url('members') ?>" class="sidebar-link <?= $activeMenu === 'members' ? 'sidebar-link-active' : '' ?>">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
        <circle cx="9" cy="7" r="4"></circle>
      </svg>
      Data Anggota
    </a>

    <a href="<?= site_url('transactions') ?>" class="sidebar-link <?= $activeMenu === 'transactions' ? 'sidebar-link-active' : '' ?>">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M8 3l4 4-4 4"></path>
        <path d="M16 21l-4-4 4-4"></path>
        <path d="M12 7h8"></path>
        <path d="M4 17h8"></path>
      </svg>
      Peminjaman
    </a>

    <a href="<?= site_url('fines') ?>" class="sidebar-link <?= $activeMenu === 'fines' ? 'sidebar-link-active' : '' ?>">
      <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <line x1="12" y1="1" x2="12" y2="23"></line>
        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
      </svg>
      Denda & Bonus
    </a>
  </nav>
</aside>
