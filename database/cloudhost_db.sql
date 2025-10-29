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

CREATE TABLE IF NOT EXISTS paket_diskon (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_paket VARCHAR(120) NOT NULL,
    deskripsi TEXT,
    harga_normal DECIMAL(12,2) NOT NULL,
    harga_diskon DECIMAL(12,2) NOT NULL,
    tanggal_mulai DATE DEFAULT NULL,
    tanggal_selesai DATE DEFAULT NULL,
    status ENUM('draft','aktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    paket_id INT NOT NULL,
    domain VARCHAR(150) NOT NULL,
    metode_pembayaran VARCHAR(60) NOT NULL,
    project_file VARCHAR(255) DEFAULT NULL,
    status ENUM('pending','paid','failed','aktif','selesai') DEFAULT 'pending',
    tanggal_pesanan DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pesanan_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_pesanan_paket FOREIGN KEY (paket_id) REFERENCES paket_hosting(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pesanan_id INT NOT NULL,
    order_id VARCHAR(100) UNIQUE,
    gross_amount DECIMAL(12,2),
    payment_type VARCHAR(50),
    transaction_status VARCHAR(50),
    transaction_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_transaksi_pesanan FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (nama, email, password, role) VALUES
('Administrator CloudHost', 'admin@cloudhost.id', '$2y$10$9jCqCtp42A6zsQ.zHHR6OOFJOLLmObAun1vDLteA94ppIqhzyap8m', 'admin'), -- password: admin123
('Budi Santoso', 'budi@pelanggan.id', '$2y$10$6qBsZv4dSKJ5IhXCTITVEOCZU97GU0RGnqDtFb18m0tHbdD.N14Wy', 'customer'), -- password: customer123
('Sinta Lestari', 'sinta@pelanggan.id', '$2y$10$6PF39/C4h/11FXGeUXLEVOL3G.yUpiRaY1oWXcnZ6FQcmGeXgnP9a', 'customer'); -- password: customer123

INSERT INTO paket_hosting (nama_paket, deskripsi, harga, fitur) VALUES
('Starter', 'Cocok untuk website personal dan portofolio.', 49000, '1 Website\n10 GB SSD Storage\nUnlimited Bandwidth\nGratis SSL'),
('Business', 'Solusi optimal untuk UMKM dengan traffic menengah.', 99000, '5 Website\n50 GB SSD Storage\nUnlimited Email\nBackup Harian'),
('Premium', 'Performa maksimal untuk website bisnis dan e-commerce.', 199000, 'Unlimited Website\nNVMe SSD Storage\nPrioritas Support\nCDN & Web Firewall');

INSERT INTO paket_diskon (nama_paket, deskripsi, harga_normal, harga_diskon, tanggal_mulai, tanggal_selesai, status) VALUES
('Starter Flash Sale', 'Diskon spesial untuk pelanggan baru CloudHost paket Starter.', 49000.00, 35000.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'aktif'),
('Business Annual Deal', 'Hemat besar untuk langganan tahunan paket Business.', 1188000.00, 829000.00, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'aktif');

INSERT INTO pesanan (user_id, paket_id, domain, metode_pembayaran, project_file, status, tanggal_pesanan) VALUES
(2, 2, 'tokobudi.com', 'Transfer Bank', NULL, 'aktif', '2024-06-10 09:00:00'),
(3, 1, 'blog-sinta.id', 'E-Wallet', NULL, 'selesai', '2024-05-22 14:30:00'),
(2, 3, 'cloudbudi.co.id', 'Kartu Kredit', NULL, 'pending', '2024-07-01 10:15:00');

INSERT INTO transaksi (pesanan_id, order_id, gross_amount, payment_type, transaction_status, transaction_time) VALUES
(3, 'CLOUDHOST-3-123456', 199000.00, 'credit_card', 'pending', '2024-07-01 10:16:00');
