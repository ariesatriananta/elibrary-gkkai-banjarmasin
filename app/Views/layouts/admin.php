<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($pageTitle ?? library_brand_name()) ?> - <?= esc(library_brand_name()) ?></title>
  <meta name="description" content="<?= esc(library_meta_description()) ?>">
  <meta name="application-name" content="<?= esc(library_brand_name()) ?>">
  <meta id="theme-color-meta" name="theme-color" content="#4c7a5e">
  <link rel="icon" href="<?= base_url('favicon.ico') ?>" sizes="any">
  <link rel="icon" type="image/png" href="<?= library_logo_url() ?>">
  <link rel="apple-touch-icon" href="<?= library_logo_url() ?>">
  <link rel="manifest" href="<?= base_url('site.webmanifest') ?>">
  <?= $this->include('partials/theme_head') ?>
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<?php $pageSkeleton = trim($this->renderSection('pageSkeleton')); ?>
<body class="app-shell">
  <div class="app-progress" aria-hidden="true">
    <div class="app-progress-track">
      <div class="app-progress-bar"></div>
    </div>
  </div>

  <div class="pointer-events-none fixed inset-0 overflow-hidden">
    <div class="page-orb page-orb-primary left-[-10rem] top-[-8rem] h-72 w-72"></div>
    <div class="page-orb page-orb-accent right-[-6rem] top-16 h-64 w-64"></div>
    <div class="page-orb page-orb-neutral bottom-[-7rem] left-1/3 h-72 w-72"></div>
  </div>

  <div class="relative flex min-h-screen items-start gap-4 px-4 py-4">
    <?= $this->include('partials/sidebar') ?>

    <main class="min-w-0 flex-1 pr-1">
      <header class="glass-header app-sticky-header mb-4 flex min-h-[4.75rem] items-center justify-between px-5 py-3">
        <div class="min-w-0">
          <p class="truncate text-lg font-semibold tracking-tight"><?= esc(library_brand_name()) ?></p>
          <p class="truncate text-xs text-slate-500"><?= esc(church_name()) ?></p>
        </div>
        <div class="flex items-center gap-3">
          <button type="button" id="theme-toggle" class="theme-toggle" aria-label="Ubah tema tampilan" aria-pressed="false">
            <span class="theme-toggle-icon theme-toggle-icon-sun" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <circle cx="12" cy="12" r="4"></circle>
                <path d="M12 2v2.5"></path>
                <path d="M12 19.5V22"></path>
                <path d="M4.93 4.93l1.77 1.77"></path>
                <path d="M17.3 17.3l1.77 1.77"></path>
                <path d="M2 12h2.5"></path>
                <path d="M19.5 12H22"></path>
                <path d="M4.93 19.07l1.77-1.77"></path>
                <path d="M17.3 6.7l1.77-1.77"></path>
              </svg>
            </span>
            <span class="theme-toggle-icon theme-toggle-icon-moon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"></path>
              </svg>
            </span>
          </button>
          <div class="text-right leading-tight">
            <p class="text-sm font-medium"><?= esc(session('admin_name') ?? 'Petugas') ?></p>
            <p class="text-xs text-slate-500">Admin Perpustakaan</p>
          </div>
          <form method="post" action="<?= site_url('logout') ?>">
            <button type="submit" class="panel-button-secondary text-xs">
              Logout
            </button>
          </form>
        </div>
      </header>

      <div id="page-content-shell" class="page-content-shell px-1 pb-6">
        <div id="page-loading-skeleton" class="page-loading-skeleton" aria-hidden="true">
          <div id="page-loading-skeleton-content">
            <?php if ($pageSkeleton !== ''): ?>
              <?= $pageSkeleton ?>
            <?php else: ?>
              <?= $this->include('partials/page_skeleton_default') ?>
            <?php endif; ?>
          </div>
        </div>

        <div class="page-live-content space-y-6">
          <?php if (session()->getFlashdata('success')): ?>
            <div class="glass-alert border-success/20 bg-success/10 text-success">
              <?= esc(session()->getFlashdata('success')) ?>
            </div>
          <?php endif; ?>

          <?php if (session()->getFlashdata('error')): ?>
            <div class="glass-alert border-destructive/20 bg-destructive/10 text-destructive">
              <?= esc(session()->getFlashdata('error')) ?>
            </div>
          <?php endif; ?>

          <?= $this->renderSection('content') ?>
        </div>
      </div>
    </main>
  </div>

  <?= $this->include('partials/page_skeleton_templates') ?>

  <script>
    (() => {
      const root = document.documentElement;
      const button = document.getElementById('theme-toggle');
      const themeColorMeta = document.getElementById('theme-color-meta');
      const body = document.body;
      const skeletonContent = document.getElementById('page-loading-skeleton-content');
      const colors = {
        light: '#4c7a5e',
        dark: '#0f172a',
      };

      const applyTheme = (theme) => {
        root.setAttribute('data-theme', theme);
        button?.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');

        if (themeColorMeta) {
          themeColorMeta.setAttribute('content', colors[theme] ?? colors.light);
        }
      };

      const getCurrentTheme = () => root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';

      applyTheme(getCurrentTheme());

      button?.addEventListener('click', () => {
        const nextTheme = getCurrentTheme() === 'dark' ? 'light' : 'dark';
        applyTheme(nextTheme);

        try {
          window.localStorage.setItem('library-theme', nextTheme);
        } catch (error) {
          // Ignore storage errors and keep the theme in memory.
        }
      });

      const startPageLoading = () => {
        body.classList.add('is-page-loading');
      };

      const stopPageLoading = () => {
        body.classList.remove('is-page-loading');
        document.querySelectorAll('.is-loading-submit').forEach((element) => {
          element.classList.remove('is-loading-submit');
          element.disabled = false;
          element.removeAttribute('aria-busy');
        });
        document.querySelectorAll('.is-loading-nav').forEach((element) => {
          element.classList.remove('is-loading-nav');
          element.removeAttribute('aria-busy');
        });
      };

      const setSkeletonTemplate = (name) => {
        if (!skeletonContent) {
          return;
        }

        const template = document.getElementById(`skeleton-template-${name}`);

        if (!template) {
          return;
        }

        skeletonContent.innerHTML = template.innerHTML;
      };

      const detectSkeletonTemplate = (href) => {
        try {
          const url = new URL(href, window.location.origin);
          const path = url.pathname.toLowerCase();

          if (path.endsWith('/transactions') || path.includes('/transactions/')) {
            return 'transactions';
          }

          if (path.endsWith('/fines') || path.includes('/fines/')) {
            return 'fines';
          }

          if (path.endsWith('/members') || path.includes('/members/')) {
            return 'members';
          }

          if (path.endsWith('/books') || path.includes('/books/')) {
            return 'books';
          }

          return 'dashboard';
        } catch (error) {
          return 'default';
        }
      };

      const shouldHandleNavigation = (event, link) => {
        if (
          event.defaultPrevented ||
          event.button !== 0 ||
          event.metaKey ||
          event.ctrlKey ||
          event.shiftKey ||
          event.altKey ||
          link.target === '_blank'
        ) {
          return false;
        }

        const destination = link.getAttribute('href');

        if (!destination || destination.startsWith('#')) {
          return false;
        }

        return true;
      };

      document.querySelectorAll('.sidebar-link[href]').forEach((link) => {
        link.addEventListener('click', (event) => {
          if (!shouldHandleNavigation(event, link)) {
            return;
          }

          setSkeletonTemplate(link.dataset.skeletonTemplate || 'default');
          link.classList.add('is-loading-nav');
          link.setAttribute('aria-busy', 'true');
          startPageLoading();
        });
      });

      document.querySelectorAll('main .page-live-content a[href].panel-button, main .page-live-content a[href].panel-button-secondary').forEach((link) => {
        link.classList.add('loading-target');

        link.addEventListener('click', (event) => {
          if (!shouldHandleNavigation(event, link)) {
            return;
          }

          setSkeletonTemplate(link.dataset.skeletonTemplate || detectSkeletonTemplate(link.href));
          link.classList.add('is-loading-submit');
          link.setAttribute('aria-busy', 'true');
          startPageLoading();
        });
      });

      document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', (event) => {
          if (event.defaultPrevented) {
            return;
          }

          const submitter = event.submitter instanceof HTMLElement
            ? event.submitter
            : form.querySelector('button[type="submit"], input[type="submit"]');

          if (submitter instanceof HTMLElement) {
            submitter.classList.add('loading-target', 'is-loading-submit');
            submitter.setAttribute('aria-busy', 'true');

            if ('disabled' in submitter) {
              submitter.disabled = true;
            }
          }

          startPageLoading();
        });
      });

      window.addEventListener('pageshow', stopPageLoading);
    })();
  </script>
  <?= $this->renderSection('scripts') ?>
</body>
</html>
