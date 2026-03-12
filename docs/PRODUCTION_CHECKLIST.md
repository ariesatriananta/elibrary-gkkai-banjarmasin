# Production Checklist

Checklist ini untuk rilis internal yang lebih aman, baik di hosting online maupun server lokal gereja.

## Environment

- Gunakan `CI_ENVIRONMENT = production`
- Pastikan `app.baseURL` sesuai domain/folder deploy
- Jika situs berjalan di HTTPS, set `app.forceGlobalSecureRequests = true`
- Jangan deploy `.env` dari laptop development
- Gunakan template [deploy/production.env.example] atau [deploy/xampp.env.example]

## File & Permission

- `writable/` harus bisa ditulis oleh PHP
- `public/assets/uploads/covers/` harus bisa ditulis oleh PHP
- Pastikan `.htaccess` ikut terdeploy
- Untuk Apache, aktifkan `mod_rewrite` dan `AllowOverride All`

## Database

- Jalankan `php spark migrate`
- Jalankan `php spark db:seed DatabaseSeeder` hanya pada instalasi baru
- Import buku awal bila diperlukan
- Uji koneksi database dari server target, bukan hanya dari laptop development

## Security

- Ganti password default admin segera setelah deploy
- Jangan biarkan `display_errors` aktif di server
- Pastikan folder project tidak menyimpan file sensitif yang tidak diperlukan publik
- Jika online, pakai HTTPS aktif dan valid

## Backup

- Simpan backup database rutin
- Simpan backup folder `public/assets/uploads/covers/`
- Uji prosedur restore minimal satu kali sebelum go-live penuh

## Final Smoke Test

- Login admin berhasil
- Dashboard tampil normal
- CRUD buku berhasil
- CRUD anggota berhasil
- Peminjaman berhasil
- Pengembalian normal berhasil
- Pengembalian telat memunculkan denda
- Pengembalian rusak memunculkan denda kerusakan
- Pengembalian hilang memunculkan kasus penggantian
- Pagination `books`, `members`, `transactions`, `fines` berjalan
- Upload cover buku berhasil dan gambar bisa diakses dari browser
