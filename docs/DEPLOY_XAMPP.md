# Deploy ke XAMPP

Panduan ini untuk menjalankan aplikasi di PC gereja lewat `Apache + MySQL` pada XAMPP tanpa Node.js dan tanpa Composer di server.

## 1. Buat paket deploy dari laptop development

Jalankan dari root project:

```powershell
php spark migrate
php spark db:seed DatabaseSeeder
npm run build:css
powershell -ExecutionPolicy Bypass -File .\tools\package-xampp.ps1
```

Hasil paket akan dibuat di:

```text
dist\xampp\elibrary-gkkai
```

Paket ini sudah membawa:

- source aplikasi
- `vendor/`
- CSS Tailwind offline hasil build
- `writable/`
- `.env` contoh untuk XAMPP

## 2. Copy ke PC gereja

Copy folder hasil paket ke:

```text
C:\xampp\htdocs\elibrary-gkkai
```

## 3. Siapkan database

1. Nyalakan `Apache` dan `MySQL` di XAMPP.
2. Buat database baru, misalnya:

```sql
CREATE DATABASE elibrary_gkkai;
```

3. Edit file:

```text
C:\xampp\htdocs\elibrary-gkkai\.env
```

Sesuaikan terutama bagian:

```ini
app.baseURL = 'http://localhost/elibrary-gkkai/'
database.default.database = elibrary_gkkai
database.default.username = root
database.default.password =
```

Kalau nama folder di `htdocs` berbeda, ubah juga `app.baseURL`.

## 4. Jalankan migrasi dan import di PC server

Buka terminal di:

```text
C:\xampp\htdocs\elibrary-gkkai
```

Lalu jalankan:

```powershell
php spark migrate
php spark db:seed DatabaseSeeder
php spark library:import-books "C:\path\ke\Data Buku Perpustakaan Tiranus (1).xls"
```

## 5. Akses aplikasi

Buka browser:

```text
http://localhost/elibrary-gkkai/
```

Login awal:

- Username: `admin`
- Password: `admin123`

## Catatan penting

- Root project sudah punya `.htaccess` supaya URL bisa langsung jalan dari `htdocs/elibrary-gkkai` tanpa perlu menulis `/public`.
- Jika Apache XAMPP belum membaca `.htaccess`, pastikan `mod_rewrite` aktif dan `AllowOverride All`.
- `node_modules/` tidak diperlukan di PC gereja.
- Jangan copy file `.env` dari laptop development. Pakai `.env` yang dibuat oleh paket deploy lalu edit sesuai server.
