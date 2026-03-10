<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($pageTitle ?? 'Elibrary GKKAI') ?> - Elibrary GKKAI</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
  <div class="min-h-screen flex w-full">
    <?= $this->include('partials/sidebar') ?>

    <main class="flex min-h-screen flex-1 flex-col">
      <header class="flex h-14 items-center justify-between border-b border-border bg-white px-4">
        <span class="text-lg font-semibold tracking-tight">Elibrary GKKAI</span>
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
