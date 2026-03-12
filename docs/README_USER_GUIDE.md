# README User Guide

## Perpustakaan GKKA-I Banjarmasin

Panduan ini ditujukan untuk petugas perpustakaan agar dapat menggunakan aplikasi dalam kegiatan operasional harian.

## 1. Login ke Sistem

Langkah:

1. Buka aplikasi di browser.
2. Masukkan `username`.
3. Masukkan `password`.
4. Klik tombol `Masuk`.

Catatan:

- Setelah berhasil login, user akan masuk ke halaman dashboard.
- Jika password awal masih default, segera ganti password melalui menu akun di kanan atas.

## 2. Mengenal Dashboard

Di dashboard, petugas dapat melihat ringkasan:

- total buku fisik
- total judul buku
- total anggota
- jumlah buku yang sedang dipinjam
- daftar keterlambatan
- transaksi terbaru

Dashboard berguna untuk melihat kondisi perpustakaan secara cepat tanpa membuka modul satu per satu.

## 3. Mengelola Data Buku

Masuk ke menu `Data Buku`.

### Menambah Buku

Langkah:

1. Klik `Tambah Buku`.
2. Isi informasi buku:
   - judul
   - pengarang
   - penerbit
   - kategori
   - klasifikasi usia
   - ISBN atau kode referensi
   - lokasi rak
   - sinopsis
   - jumlah copy awal
3. Jika ada, upload cover buku.
4. Klik `Simpan Buku`.

Hasil:

- sistem membuat judul buku baru
- sistem membuat copy awal otomatis
- setiap copy mendapat kode sistem otomatis

### Mengedit Buku

Langkah:

1. Cari buku pada halaman `Data Buku`.
2. Klik icon `kaca pembesar`.
3. Ubah data yang diperlukan.
4. Klik `Simpan Perubahan`.

### Menghapus Buku

Langkah:

1. Cari buku pada halaman `Data Buku`.
2. Klik icon `hapus`.
3. Konfirmasi penghapusan.

Catatan:

- Buku yang sudah memiliki histori transaksi tidak bisa dihapus.

## 4. Mengelola Copy Buku

Copy buku adalah eksemplar fisik dari satu judul buku.

Di halaman edit buku, petugas dapat:

- melihat daftar copy
- mengubah kode lama
- mengubah barcode manual
- mengubah status copy
- menambah catatan copy
- menambah copy baru
- menghapus copy yang belum memiliki histori transaksi

Catatan:

- `Kode Sistem` dibuat otomatis oleh sistem.
- `Barcode Manual` dapat diisi sesuai barcode fisik yang digunakan.
- `Kode Lama` dipakai untuk menyimpan referensi kode lama dari data sebelumnya.

## 5. Mengelola Data Anggota

Masuk ke menu `Data Anggota`.

### Menambah Anggota

Langkah:

1. Klik `Tambah Anggota`.
2. Isi nama lengkap.
3. Isi telepon, email, dan alamat jika ada.
4. Tentukan status anggota.
5. Klik `Simpan Anggota`.

Catatan:

- Nomor anggota dibuat otomatis oleh sistem.

### Mengedit Anggota

Langkah:

1. Cari anggota pada daftar.
2. Klik `Kelola`.
3. Perbarui data anggota.
4. Klik `Simpan Perubahan`.

### Menonaktifkan Anggota

Gunakan status `Nonaktif` bila anggota tidak lagi dipakai untuk transaksi baru.

### Menghapus Anggota

Anggota hanya bisa dihapus bila belum pernah memiliki histori transaksi.

## 6. Mencatat Peminjaman Buku

Masuk ke menu `Peminjaman`.
Pilih tab `Pinjam Buku`.

Langkah:

1. Pilih `Anggota`.
   - Dropdown sudah bisa dicari langsung.
2. Pilih `Copy Buku`.
   - Dropdown juga bisa dicari langsung.
3. Isi `Tanggal Pinjam`.
4. Isi `Tanggal Jatuh Tempo`.
5. Tambahkan catatan bila diperlukan.
6. Klik `Simpan Peminjaman`.

Hasil:

- transaksi peminjaman tersimpan
- status copy berubah menjadi dipinjam

## 7. Mencatat Pengembalian Buku

Masuk ke menu `Peminjaman`.
Pilih tab `Kembalikan Buku`.

Langkah:

1. Pilih `Pinjaman Aktif`.
   - Dropdown bisa dicari langsung.
2. Isi `Tanggal Kembali`.
3. Pilih `Kondisi Pengembalian`:
   - `Baik`
   - `Rusak`
   - `Hilang`
4. Periksa ringkasan pengembalian yang muncul otomatis.
5. Tambahkan catatan bila diperlukan.
6. Klik `Simpan Pengembalian`.

## 8. Memahami Ringkasan Pengembalian

Sebelum pengembalian disimpan, sistem menampilkan preview:

- tanggal pinjam
- tanggal jatuh tempo
- jumlah hari keterlambatan
- estimasi denda
- penjelasan kondisi pengembalian

Tujuan preview ini adalah membantu petugas memastikan transaksi sudah benar sebelum disimpan.

## 9. Mengelola Denda dan Bonus

Masuk ke menu `Denda & Bonus`.

### Melihat Kasus Denda

Di halaman ini petugas dapat melihat:

- denda keterlambatan
- denda kerusakan buku
- kasus kehilangan buku
- status pembayaran
- status penggantian buku

### Mengubah Aturan Denda

Langkah:

1. Klik tombol `Aturan Denda` di kanan atas.
2. Ubah nilai yang diperlukan.
3. Klik `Simpan Aturan`.

### Mencatat Pembayaran Denda

Langkah:

1. Cari kasus denda.
2. Isi nominal pembayaran.
3. Klik `Simpan`.

### Menyelesaikan Kasus Buku Hilang

Langkah:

1. Cari kasus kehilangan buku.
2. Isi catatan penyelesaian jika diperlukan.
3. Klik `Tandai Selesai`.

Gunakan langkah ini saat buku pengganti sudah diterima.

### Menambah Catatan Bonus

Petugas dapat menambahkan catatan manual sebagai apresiasi atau catatan khusus pada transaksi.

## 10. Riwayat Transaksi

Pada menu `Peminjaman`, tab `Riwayat` menampilkan histori transaksi.

Petugas dapat:

- mencari transaksi
- memfilter berdasarkan status
- berpindah halaman dengan pagination

## 11. Penjelasan Status

### Status Buku / Copy

- `Tersedia`: copy bisa dipinjam
- `Dipinjam`: copy sedang dipinjam
- `Hilang`: copy berada pada kasus kehilangan

### Status Anggota

- `Aktif`: anggota dapat dipakai untuk peminjaman
- `Nonaktif`: anggota tidak dapat dipakai untuk peminjaman baru

### Status Pinjaman

- `Dipinjam`: buku sedang dipinjam
- `Terlambat`: buku belum dikembalikan dan sudah lewat jatuh tempo
- `Dikembalikan`: transaksi selesai normal
- `Hilang`: transaksi ditandai hilang

### Kondisi Pengembalian

- `Baik`: dikembalikan normal
- `Rusak`: dikembalikan rusak
- `Hilang`: buku dinyatakan hilang

### Status Denda

- `Belum Lunas`: belum ada atau belum cukup pembayaran
- `Cicil`: pembayaran baru sebagian
- `Lunas`: pembayaran selesai
- `Menunggu Penggantian`: kasus kehilangan masih terbuka
- `Selesai`: kasus kehilangan sudah diselesaikan

## 12. Pencarian dan Filter

Sistem menyediakan pencarian dan filter di beberapa modul utama:

- Data Buku
- Data Anggota
- Riwayat Transaksi
- Denda & Bonus

Gunakan fitur ini untuk mempercepat pencarian data saat jumlah data sudah banyak.

## 13. Pagination

Untuk menjaga aplikasi tetap ringan, beberapa halaman sudah memakai pagination:

- daftar buku
- daftar anggota
- riwayat transaksi
- riwayat anggota
- daftar denda

Gunakan tombol `Sebelumnya`, `Berikutnya`, atau nomor halaman untuk berpindah data.

## 14. Ganti Password

Langkah:

1. Klik icon user di kanan atas.
2. Pilih `Ganti Password`.
3. Isi password saat ini.
4. Isi password baru.
5. Konfirmasi password baru.
6. Klik `Simpan Password`.

## 15. Logout

Langkah:

1. Klik icon user di kanan atas.
2. Klik `Logout`.

## 16. Tips Penggunaan

- Isi data buku secara lengkap agar pencarian lebih mudah.
- Gunakan barcode manual jika perpustakaan memiliki label fisik.
- Nonaktifkan anggota lama daripada menghapus jika sudah punya histori.
- Selalu cek preview pengembalian sebelum menyimpan.
- Segera catat pembayaran denda agar status denda tetap akurat.

## 17. Penanganan Masalah Umum

### Tidak bisa menghapus buku

Penyebab:

- buku sudah memiliki histori transaksi

Solusi:

- biarkan data tetap ada di sistem
- kelola copy atau gunakan status yang sesuai

### Tidak bisa menghapus anggota

Penyebab:

- anggota sudah memiliki histori transaksi

Solusi:

- ubah status anggota menjadi `Nonaktif`

### Cover buku tidak tampil

Penyebab umum:

- file upload belum berhasil
- file belum ikut terdeploy
- permission folder upload belum benar

## 18. Catatan Penutup

Panduan ini dibuat untuk penggunaan operasional harian. Untuk kebutuhan teknis seperti deploy, backup, atau konfigurasi server, gunakan dokumentasi teknis terpisah.
