<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Elibrary GKKAI</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="min-h-screen bg-background">
  <div class="flex min-h-screen items-center justify-center px-4 py-8">
    <div class="grid w-full max-w-5xl gap-6 lg:grid-cols-[1.2fr_0.8fr]">
      <div class="hidden rounded-3xl border border-border bg-gradient-to-br from-primary via-primary/90 to-accent p-10 text-white shadow-panel lg:block">
        <div class="max-w-md space-y-6">
          <span class="inline-flex rounded-full bg-white/20 px-3 py-1 text-xs font-medium">Elibrary GKKAI</span>
          <div class="space-y-3">
            <h1 class="text-4xl font-bold tracking-tight">Sistem perpustakaan gereja yang ringan dan rapi.</h1>
            <p class="text-sm leading-6 text-white/90">
              Kelola buku, anggota, peminjaman, keterlambatan, dan denda dari satu dashboard yang tetap sederhana untuk operasional harian.
            </p>
          </div>
        </div>
      </div>

      <div class="panel-card p-8">
        <div class="mb-8 space-y-2">
          <p class="text-sm font-medium text-primary">Login Petugas</p>
          <h2 class="text-2xl font-bold tracking-tight">Masuk ke Elibrary GKKAI</h2>
          <p class="text-sm text-slate-500">Gunakan akun admin untuk mengakses dashboard perpustakaan.</p>
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

        <form method="post" action="<?= site_url('login') ?>" class="space-y-4">
          <div>
            <label for="username" class="mb-1 block text-sm font-medium">Username</label>
            <input id="username" name="username" type="text" value="<?= esc(old('username')) ?>" class="panel-input" placeholder="admin">
          </div>

          <div>
            <label for="password" class="mb-1 block text-sm font-medium">Password</label>
            <input id="password" name="password" type="password" class="panel-input" placeholder="••••••••">
          </div>

          <button type="submit" class="panel-button w-full justify-center">Masuk</button>
        </form>

        <div class="mt-6 rounded-xl bg-muted px-4 py-3 text-xs text-slate-600">
          Akun seed awal: <strong>admin</strong> / <strong>admin123</strong>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
