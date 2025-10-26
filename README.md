# CloudHost - Layanan Jasa Cloud & Hosting

Proyek ini adalah aplikasi website fullstack sederhana berbasis PHP native dengan konsep layanan Cloud & Hosting bernama **CloudHost**. Aplikasi menyediakan peran Admin dan Customer untuk mengelola paket hosting, pelanggan, serta pesanan.

## Fitur Utama

### Publik
- Landing page modern dengan hero, fitur, paket harga, dan CTA.
- Halaman Tentang Kami dan Kontak.

### Customer
- Registrasi dan login pelanggan menggunakan session.
- Dashboard pelanggan dengan ringkasan pesanan aktif.
- Form pemesanan hosting baru lengkap dengan unggah folder project dalam format ZIP (maks. 10 MB).
- Riwayat pesanan dengan status (menunggu, aktif, selesai) dan tautan unduhan file project.

### Admin
- Login admin menggunakan session.
- Dashboard admin dengan ringkasan pelanggan, pesanan, dan pendapatan.
- CRUD paket hosting.
- Manajemen pelanggan dan pesanan termasuk perubahan status.

## Struktur Folder
```
cloudhost/
├── config/
├── database/
├── partials/
└── public/
    ├── admin/
    ├── assets/
    ├── customer/
    ├── about.php
    ├── contact.php
    └── index.php
```

## Persiapan Database
1. Import file `database/cloudhost_db.sql` ke dalam MySQL Anda.
2. Gunakan kredensial default:
   - Admin: `admin@cloudhost.id` / `admin123`
   - Customer: `budi@pelanggan.id` / `customer123`

## Konfigurasi
Sesuaikan koneksi database pada `config/config.php` jika diperlukan. File ini juga akan membuat folder `public/uploads/projects` secara otomatis untuk menyimpan arsip project yang diunggah customer. Pastikan server memiliki izin tulis pada direktori tersebut.

### Batasan Unggah Project
- Format file wajib `.zip`.
- Ukuran maksimal 10 MB (sesuaikan dengan `php.ini` apabila diperlukan).
- Arsip yang diunggah akan tersedia bagi Admin dan customer melalui tautan unduhan pada tabel pesanan.

## Menjalankan Aplikasi
Gunakan server PHP bawaan dengan root direktori `public/`:
```bash
php -S localhost:8000 -t public
```

Kemudian akses `http://localhost:8000` di browser.
