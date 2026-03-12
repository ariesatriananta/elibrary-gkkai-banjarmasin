# UAT Release Checklist

Gunakan checklist ini sebelum go-live atau sebelum update besar dipasang ke server aktif.

## Auth & Session

- Login dengan akun admin berhasil
- Logout berhasil
- Ganti password berhasil
- Setelah refresh halaman, session tetap valid

## Dashboard

- `Total Buku` menampilkan total copy fisik
- `Total Judul Buku` menampilkan total judul
- `Total Anggota`, `Sedang Dipinjam`, `Keterlambatan`, dan `Transaksi Terbaru` tampil benar

## Buku

- Tambah buku baru berhasil
- Edit metadata buku berhasil
- Upload cover berhasil
- Tambah copy buku berhasil
- Edit barcode manual / kode lama berhasil
- Hapus buku yang belum punya histori berhasil
- Hapus buku yang sudah punya histori ditolak dengan pesan yang benar
- Filter, pencarian, dan pagination bekerja

## Anggota

- Tambah anggota berhasil
- Edit anggota berhasil
- Nonaktifkan anggota berhasil
- Hapus anggota tanpa histori berhasil
- Hapus anggota yang punya histori ditolak
- Filter, pencarian, dan pagination bekerja

## Peminjaman

- Field `Anggota` dapat dicari
- Field `Copy Buku` dapat dicari
- Peminjaman buku berhasil
- Status copy dan status buku ikut berubah

## Pengembalian

- Field `Pinjaman Aktif` dapat dicari
- Preview tanggal pinjam, jatuh tempo, dan estimasi denda muncul sebelum submit
- Pengembalian normal berhasil
- Pengembalian telat memunculkan denda mingguan setelah masa tenggang
- Pengembalian rusak menambah denda kerusakan
- Pengembalian hilang membuat kasus penggantian buku

## Denda & Bonus

- Pengaturan denda bisa dibuka dari modal dan disimpan
- Pembayaran denda berhasil
- Kasus kehilangan bisa ditandai selesai
- Catatan bonus berhasil ditambahkan
- Filter, pencarian, dan pagination bekerja

## Tampilan

- Light mode normal
- Dark mode normal
- Sidebar desktop tetap rapi
- Sidebar mobile off-canvas bekerja
- Header sticky bekerja
- Loading skeleton dan progress bar tampil normal

## File & Deploy

- Logo tampil di login dan admin
- Gambar gereja tampil di login
- Cover upload tampil setelah upload
- `logo.png`, `gedung-gereja.png`, dan asset publik lain bisa diakses setelah deploy
