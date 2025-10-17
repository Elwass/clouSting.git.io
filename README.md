# CloudHost - Layanan Jasa Cloud & Hosting

Proyek ini adalah aplikasi website fullstack sederhana berbasis PHP native dengan konsep layanan Cloud & Hosting bernama **CloudHost**. Aplikasi menyediakan peran Admin dan Customer untuk mengelola paket hosting, pelanggan, serta pesanan.

## Fitur Utama

### Publik
- Landing page modern dengan hero, fitur, paket harga, dan CTA.
- Halaman Tentang Kami dan Kontak.

### Customer
- Registrasi dan login pelanggan menggunakan session.
- Dashboard pelanggan dengan ringkasan pesanan aktif.
- Form pemesanan hosting baru.
- Riwayat pesanan dengan status (menunggu, aktif, selesai).

### Admin
- Login admin menggunakan session.
- Dashboard admin dengan ringkasan pelanggan, pesanan, dan pendapatan.
- CRUD paket hosting.
- Manajemen pelanggan dan pesanan termasuk perubahan status.

## Struktur Folder
```
cloudhost/
├── admin/
├── config/
├── customer/
├── database/
├── partials/
└── public/
```

## Persiapan Database
1. Import file `database/cloudhost_db.sql` ke dalam MySQL Anda.
2. Gunakan kredensial default:
   - Admin: `admin@cloudhost.id` / `admin123`
   - Customer: `budi@pelanggan.id` / `customer123`

## Konfigurasi
Sesuaikan koneksi database pada `config/config.php` jika diperlukan.

## Menjalankan Aplikasi
Gunakan server PHP bawaan:
```bash
php -S localhost:8000 -t public
```

Kemudian akses `http://localhost:8000` di browser.
