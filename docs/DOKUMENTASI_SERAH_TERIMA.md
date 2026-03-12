# Dokumentasi Serah Terima

## Identitas Aplikasi

- Nama aplikasi: **Perpustakaan GKKA-I Banjarmasin**
- Institusi:
  **GKKA INDONESIA Jemaat Banjarmasin**
  Jl. Veteran No. 85 RT. 11, Kelurahan Melayu, Banjarmasin Tengah, Kalimantan Selatan, 70234.
- Jenis aplikasi: Sistem perpustakaan berbasis web untuk penggunaan internal

## Tujuan Sistem

Aplikasi ini dibuat untuk membantu pengelolaan perpustakaan gereja dalam satu sistem terpusat, meliputi:

- pengelolaan data buku
- pengelolaan data anggota
- pencatatan peminjaman dan pengembalian
- pengelolaan denda
- pemantauan aktivitas perpustakaan melalui dashboard

## Ruang Lingkup Modul

### 1. Dashboard

Fungsi utama dashboard:

- menampilkan total copy buku fisik
- menampilkan total judul buku
- menampilkan total anggota
- menampilkan jumlah buku yang sedang dipinjam
- menampilkan daftar keterlambatan secara ringkas
- menampilkan transaksi terbaru

Tujuan dashboard adalah memberi gambaran cepat kondisi perpustakaan saat ini.

### 2. Modul Data Buku

Fitur yang tersedia:

- tambah buku
- edit buku
- hapus buku
- upload cover buku
- pengelolaan kategori buku
- pengelolaan klasifikasi usia
- input ISBN atau kode referensi
- input lokasi rak
- input status data lama
- pencatatan sinopsis
- pencarian dan filter data buku
- pagination daftar buku

Catatan penting:

- Sistem membedakan antara **judul buku** dan **copy buku**.
- Satu judul buku dapat memiliki banyak copy fisik.
- Setiap copy buku memiliki **kode sistem otomatis**.
- Barcode manual dan kode lama tetap dapat diinput per copy.

### 3. Modul Copy Buku

Fitur yang tersedia:

- tambah copy buku
- edit kode lama copy
- edit barcode manual copy
- ubah status copy
- tambah catatan copy
- hapus copy buku jika belum memiliki histori transaksi

Tujuan modul ini adalah memastikan stok dihitung dari jumlah copy fisik, bukan angka manual.

### 4. Modul Data Anggota

Fitur yang tersedia:

- tambah anggota
- edit anggota
- hapus anggota jika belum punya histori transaksi
- nomor anggota otomatis
- input telepon
- input email
- input alamat
- status aktif atau nonaktif
- riwayat peminjaman anggota
- pencarian, filter, dan pagination daftar anggota

### 5. Modul Peminjaman

Fitur yang tersedia:

- pilih anggota
- pilih copy buku
- pencarian cepat pada dropdown anggota
- pencarian cepat pada dropdown copy buku
- tanggal pinjam
- tanggal jatuh tempo
- catatan peminjaman

Hasil saat peminjaman berhasil:

- transaksi pinjam tercatat
- status copy berubah menjadi dipinjam
- status ketersediaan buku ikut diperbarui

### 6. Modul Pengembalian

Fitur yang tersedia:

- pilih pinjaman aktif
- pencarian cepat pada dropdown pinjaman aktif
- tanggal kembali
- kondisi pengembalian
- catatan pengembalian
- preview ringkasan pengembalian sebelum disimpan

Informasi preview yang tampil sebelum simpan:

- tanggal pinjam
- tanggal jatuh tempo
- jumlah hari keterlambatan
- estimasi denda
- keterangan sesuai kondisi pengembalian

### 7. Modul Riwayat Transaksi

Fitur yang tersedia:

- menampilkan histori peminjaman dan pengembalian
- filter status transaksi
- pencarian anggota, buku, atau kode copy
- pagination daftar riwayat

### 8. Modul Denda dan Bonus

Fitur yang tersedia:

- pengaturan denda melalui modal Aturan Denda
- denda keterlambatan per minggu
- masa tenggang keterlambatan
- denda kerusakan buku
- durasi pinjam default
- pembayaran denda
- penyelesaian kasus buku hilang
- catatan bonus manual
- pencarian, filter, dan pagination daftar kasus denda

## Aturan Denda yang Berlaku

Sistem saat ini mendukung tiga jenis kasus denda:

### 1. Keterlambatan

- tipe: `late`
- denda dihitung **per minggu**
- nominal default: **Rp 5.000 per minggu**
- denda baru aktif setelah melewati **masa tenggang 3 hari** setelah jatuh tempo

### 2. Kerusakan Buku

- tipe: `damage`
- nominal default: **Rp 100.000 per buku**
- dikenakan saat petugas memilih kondisi pengembalian `Rusak`

### 3. Kehilangan Buku

- tipe: `lost`
- tidak dikenakan nominal uang otomatis
- anggota wajib mengganti buku dalam kondisi baru atau bekas layak baca
- kasus dinyatakan selesai saat petugas menandai penggantian buku sudah diterima

## Alur Sistem

### A. Alur Pengelolaan Buku

1. Petugas menambahkan judul buku baru.
2. Sistem menyimpan data metadata buku.
3. Sistem membuat copy awal sesuai jumlah copy saat input.
4. Setiap copy mendapat kode sistem otomatis.
5. Petugas dapat menambah copy baru kapan saja.

### B. Alur Pendaftaran Anggota

1. Petugas mengisi data anggota.
2. Sistem membuat nomor anggota otomatis.
3. Anggota dapat diaktifkan atau dinonaktifkan.

### C. Alur Peminjaman

1. Petugas memilih anggota.
2. Petugas memilih copy buku yang tersedia.
3. Petugas menentukan tanggal pinjam dan jatuh tempo.
4. Sistem menyimpan transaksi peminjaman.
5. Status copy berubah menjadi `Dipinjam`.

### D. Alur Pengembalian Normal

1. Petugas memilih pinjaman aktif.
2. Petugas mengisi tanggal kembali.
3. Petugas memilih kondisi `Baik`.
4. Sistem menghitung apakah ada keterlambatan.
5. Jika tidak telat, transaksi selesai tanpa denda.
6. Jika telat melebihi masa tenggang, sistem membuat denda keterlambatan.
7. Status copy kembali tersedia.

### E. Alur Pengembalian Rusak

1. Petugas memilih pinjaman aktif.
2. Petugas mengisi tanggal kembali.
3. Petugas memilih kondisi `Rusak`.
4. Sistem menghitung denda keterlambatan jika ada.
5. Sistem menambahkan denda kerusakan buku.
6. Status copy kembali tersedia setelah transaksi disimpan.

### F. Alur Buku Hilang

1. Petugas memilih pinjaman aktif.
2. Petugas mengisi tanggal kembali sesuai pelaporan.
3. Petugas memilih kondisi `Hilang`.
4. Sistem membuat kasus kehilangan buku.
5. Status pinjaman menjadi `Hilang`.
6. Status copy ditandai `Hilang`.
7. Saat buku pengganti diterima, petugas menandai kasus selesai dari modul denda.
8. Setelah diselesaikan, copy dapat kembali aktif.

## Penjelasan Status di Sistem

### Status Pinjaman

- `Dipinjam`: buku sedang dipinjam dan belum melewati jatuh tempo
- `Terlambat`: buku belum dikembalikan dan sudah melewati jatuh tempo
- `Dikembalikan`: transaksi pinjaman sudah selesai normal
- `Hilang`: pinjaman ditandai hilang dan menunggu penyelesaian penggantian

### Kondisi Pengembalian

- `Baik`: buku dikembalikan normal
- `Rusak`: buku dikembalikan dalam kondisi rusak
- `Hilang`: buku dinyatakan hilang

### Status Anggota

- `Aktif`: anggota bisa digunakan untuk peminjaman
- `Nonaktif`: anggota tidak dapat dipilih untuk peminjaman baru

### Status Copy Buku

- `Tersedia`: copy buku dapat dipinjam
- `Dipinjam`: copy buku sedang dipinjam
- `Hilang`: copy buku sedang berada pada kasus kehilangan

### Status Denda

- `Belum Lunas`: denda nominal belum dibayar
- `Cicil`: denda nominal sudah dibayar sebagian
- `Lunas`: denda nominal sudah dibayar penuh
- `Menunggu Penggantian`: kasus kehilangan masih terbuka
- `Selesai`: kasus kehilangan sudah diselesaikan

## Terminologi Penting

- **Judul Buku**: data utama buku, misalnya nama buku, pengarang, penerbit
- **Copy Buku**: eksemplar fisik dari judul buku
- **Kode Sistem Copy**: kode otomatis yang dibuat sistem untuk setiap copy
- **Barcode Manual**: kode/barcode yang diinput manual oleh petugas
- **Kode Lama**: kode lama dari data sebelumnya yang tetap disimpan sebagai referensi

## Fitur Pencarian dan Pagination

Untuk menjaga performa saat data bertambah, sistem saat ini sudah menerapkan pencarian, filter, dan pagination pada:

- daftar buku
- daftar anggota
- riwayat transaksi
- daftar denda
- riwayat peminjaman anggota

## Catatan Operasional

- Password admin awal harus segera diganti setelah instalasi
- Backup database perlu dilakukan secara rutin
- Backup folder cover buku juga perlu dilakukan jika cover digunakan aktif
- Jika jumlah data sangat besar di masa depan, dropdown pencarian dapat ditingkatkan lagi menjadi pencarian berbasis AJAX

## Penutup

Dokumentasi ini menggambarkan fitur dan alur sistem sesuai implementasi saat ini. Jika di kemudian hari ada pengembangan tambahan, dokumen ini sebaiknya ikut diperbarui agar tetap selaras dengan aplikasi yang digunakan user.
