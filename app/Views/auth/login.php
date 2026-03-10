<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - <?= esc(library_brand_name()) ?></title>
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
<body class="auth-shell min-h-screen">
  <div class="flex min-h-screen items-center justify-center px-4 py-8 lg:px-8">
    <div class="grid w-full max-w-6xl gap-6 lg:grid-cols-[1.15fr_0.85fr]">
      <div class="relative hidden min-h-[720px] overflow-hidden rounded-[2rem] border border-white/30 shadow-panel lg:block">
        <img src="<?= base_url('gedung-gereja.png') ?>" alt="<?= esc(church_name()) ?>" class="absolute inset-0 h-full w-full object-cover">
        <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(13,22,18,0.12)_0%,rgba(13,22,18,0.28)_30%,rgba(13,22,18,0.72)_100%)]"></div>
        <div class="absolute inset-x-0 bottom-0 p-8">
          <div class="max-w-xl rounded-[1.75rem] border border-white/15 bg-black/20 p-7 text-white backdrop-blur-md">
            <p class="text-xs font-medium uppercase tracking-[0.28em] text-white/70">Perpustakaan Gereja</p>
            <h1 class="mt-4 text-4xl font-bold tracking-tight"><?= esc(library_brand_name()) ?></h1>
            <p class="mt-4 text-sm leading-7 text-white/85">
              Sistem perpustakaan digital untuk pengelolaan buku, anggota, peminjaman, dan denda secara rapi dalam satu tempat.
            </p>
            <div class="mt-6 space-y-2 text-sm text-white/78">
              <p class="font-semibold text-white"><?= esc(church_name()) ?></p>
              <p class="leading-6"><?= esc(church_address()) ?></p>
            </div>
          </div>
        </div>
      </div>

      <div class="rounded-[2rem] border border-white/70 bg-white/90 p-8 shadow-panel backdrop-blur xl:p-10">
        <div class="mb-8">
          <div class="flex items-center gap-4">
            <img src="<?= library_logo_url() ?>" alt="<?= esc(library_brand_name()) ?>" class="h-16 w-16 rounded-2xl border border-border bg-white p-2 object-contain shadow-sm">
            <div class="min-w-0">
              <p class="text-lg font-semibold tracking-tight text-foreground"><?= esc(library_brand_name()) ?></p>
              <p class="mt-1 text-sm text-slate-500"><?= esc(church_name()) ?></p>
            </div>
          </div>
          <div class="mt-6 space-y-2">
            <p class="text-sm font-medium text-primary">Login Petugas</p>
            <p class="text-sm leading-6 text-slate-500">
              Gunakan akun admin untuk mengakses sistem perpustakaan dan operasional pelayanan buku.
            </p>
          </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
          <div class="mb-4 rounded-xl border border-success/20 bg-success/10 px-4 py-3 text-sm text-success">
            <?= esc(session()->getFlashdata('success')) ?>
          </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
          <div class="mb-4 rounded-xl border border-destructive/20 bg-destructive/10 px-4 py-3 text-sm text-destructive">
            <?= esc(session()->getFlashdata('error')) ?>
          </div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('login') ?>" class="space-y-5">
          <div>
            <label for="username" class="mb-2 block text-sm font-medium">Username</label>
            <input id="username" name="username" type="text" value="<?= esc(old('username')) ?>" class="panel-input h-11" placeholder="admin">
          </div>

          <div>
            <label for="password" class="mb-2 block text-sm font-medium">Password</label>
            <input id="password" name="password" type="password" class="panel-input h-11" placeholder="........">
          </div>

          <button type="submit" class="panel-button w-full justify-center py-3 text-sm">Masuk</button>
        </form>

        <div class="mt-6 rounded-2xl border border-border bg-muted/70 px-4 py-4 text-xs text-slate-600">
          Akun seed awal: <strong>admin</strong> / <strong>admin123</strong>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
