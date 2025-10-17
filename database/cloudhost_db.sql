CREATE DATABASE IF NOT EXISTS cloudhost_db;
USE cloudhost_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(120) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','customer') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS paket_hosting (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_paket VARCHAR(100) NOT NULL,
    deskripsi VARCHAR(255) NULL,
    harga INT NOT NULL,
    fitur TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    paket_id INT NOT NULL,
    domain VARCHAR(150) NOT NULL,
    metode_pembayaran VARCHAR(60) NOT NULL,
    status ENUM('menunggu','aktif','selesai') DEFAULT 'menunggu',
    tanggal_pesanan DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pesanan_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_pesanan_paket FOREIGN KEY (paket_id) REFERENCES paket_hosting(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (nama, email, password, role) VALUES
('Administrator CloudHost', 'admin@cloudhost.id', '$2y$10$9jCqCtp42A6zsQ.zHHR6OOFJOLLmObAun1vDLteA94ppIqhzyap8m', 'admin'), -- password: admin123
('Budi Santoso', 'budi@pelanggan.id', '$2y$10$6qBsZv4dSKJ5IhXCTITVEOCZU97GU0RGnqDtFb18m0tHbdD.N14Wy', 'customer'), -- password: customer123
('Sinta Lestari', 'sinta@pelanggan.id', '$2y$10$6PF39/C4h/11FXGeUXLEVOL3G.yUpiRaY1oWXcnZ6FQcmGeXgnP9a', 'customer'); -- password: customer123

INSERT INTO paket_hosting (nama_paket, deskripsi, harga, fitur) VALUES
('Starter', 'Cocok untuk website personal dan portofolio.', 49000, '1 Website\n10 GB SSD Storage\nUnlimited Bandwidth\nGratis SSL'),
('Business', 'Solusi optimal untuk UMKM dengan traffic menengah.', 99000, '5 Website\n50 GB SSD Storage\nUnlimited Email\nBackup Harian'),
('Premium', 'Performa maksimal untuk website bisnis dan e-commerce.', 199000, 'Unlimited Website\nNVMe SSD Storage\nPrioritas Support\nCDN & Web Firewall');

INSERT INTO pesanan (user_id, paket_id, domain, metode_pembayaran, status, tanggal_pesanan) VALUES
(2, 2, 'tokobudi.com', 'Transfer Bank', 'aktif', '2024-06-10 09:00:00'),
(3, 1, 'blog-sinta.id', 'E-Wallet', 'selesai', '2024-05-22 14:30:00'),
(2, 3, 'cloudbudi.co.id', 'Kartu Kredit', 'menunggu', '2024-07-01 10:15:00');
