<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($pageTitle ?? library_brand_name()) ?> - <?= esc(library_brand_name()) ?></title>
  <meta name="description" content="<?= esc(library_meta_description()) ?>">
  <meta name="application-name" content="<?= esc(library_brand_name()) ?>">
  <meta name="theme-color" content="#4c7a5e">
  <link rel="icon" href="<?= base_url('favicon.ico') ?>" sizes="any">
  <link rel="icon" type="image/png" href="<?= library_logo_url() ?>">
  <link rel="apple-touch-icon" href="<?= library_logo_url() ?>">
  <link rel="manifest" href="<?= base_url('site.webmanifest') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
  <div class="min-h-screen flex w-full">
    <?= $this->include('partials/sidebar') ?>

    <main class="flex min-h-screen flex-1 flex-col">
      <header class="flex min-h-[4.5rem] items-center justify-between border-b border-border bg-white px-4 py-3">
        <div class="min-w-0">
          <p class="truncate text-lg font-semibold tracking-tight"><?= esc(library_brand_name()) ?></p>
          <p class="truncate text-xs text-slate-500"><?= esc(church_name()) ?></p>
        </div>
        <div class="flex items-center gap-3">
          <div class="text-right leading-tight">
            <p class="text-sm font-medium"><?= esc(session('admin_name') ?? 'Petugas') ?></p>
            <p class="text-xs text-slate-500">Admin Perpustakaan</p>
          </div>
          <form method="post" action="<?= site_url('logout') ?>">
            <button type="submit" class="rounded-lg border border-border px-3 py-2 text-xs font-medium text-slate-600 hover:bg-muted">
              Logout
            </button>
          </form>
        </div>
      </header>

      <div class="flex-1 space-y-6 overflow-auto p-6">
        <?php if (session()->getFlashdata('success')): ?>
          <div class="rounded-xl border border-success/20 bg-success/10 px-4 py-3 text-sm text-success">
            <?= esc(session()->getFlashdata('success')) ?>
          </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
          <div class="rounded-xl border border-destructive/20 bg-destructive/10 px-4 py-3 text-sm text-destructive">
            <?= esc(session()->getFlashdata('error')) ?>
          </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
      </div>
    </main>
  </div>

  <?= $this->renderSection('scripts') ?>
</body>
</html>
