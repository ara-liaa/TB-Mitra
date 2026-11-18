CREATE DATABASE IF NOT EXISTS db_tb_mitra;
USE db_tb_mitra;

-- 1. Tabel Kategori Produk
CREATE TABLE kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    ikon VARCHAR(50) NOT NULL -- class Bootstrap Icons atau nama file gambar
);

INSERT INTO kategori (nama_kategori, ikon) VALUES
('Semen', 'bi-box-seam'),
('Cat Tembok', 'bi-paint-bucket'),
('Bata Merah', 'bi-bricks'),
('Pipa PVC', 'bi-bezier2'),
('Keramik & Granit', 'bi-grid'),
('Kayu & Triplek', 'bi-list'),
('Alat Teknik', 'bi-wrench-adjustable'),
('Perlengkapan Sanitasi', 'bi-droplet');

-- 2. Tabel Produk
CREATE TABLE produk (
    id_produk INT AUTO_INCREMENT PRIMARY KEY,
    id_kategori INT NOT NULL,
    nama_produk VARCHAR(150) NOT NULL,
    harga DECIMAL(12,0) NOT NULL,
    stok INT DEFAULT 100,
    gambar VARCHAR(255) NOT NULL,
    is_pilihan TINYINT(1) DEFAULT 0, -- 1 = Tampil di Produk Pilihan Beranda
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori) ON DELETE CASCADE
);

INSERT INTO produk (id_kategori, nama_produk, harga, stok, gambar, is_pilihan) VALUES
(4, 'Lem Pipa PVC', 11000, 50, 'lem-pipa.jpg', 1),
(8, 'Wastafel cuci piring', 160000, 15, 'wastafel.jpg', 1),
(4, 'RING BOSS-UK 1 cm', 6500, 120, 'ring-boss.jpg', 1),
(7, 'Terminal Dua Lubang', 26500, 40, 'terminal-listrik.jpg', 1),
(6, 'Paku Kayu 5cm/Kg', 20000, 80, 'paku-kayu.jpg', 1),
(2, 'Bak Baki Roller', 12000, 35, 'bak-roller.jpg', 1),
(1, 'Semen', 30000, 200, 'semen.jpg', 0),
(4, 'Knee PVC 20 mm', 4000, 150, 'knee-pvc.jpg', 0),
(7, 'Stop Kontak Arde', 30000, 60, 'stop-kontak.jpg', 0),
(7, 'Saklar Tunggal Broco', 45000, 45, 'saklar-broco.jpg', 0),
(4, 'Seal Tape Andaru', 5000, 100, 'seal-tape.jpg', 0),
(7, 'Fitting Lampu Tempel E27', 15000, 75, 'fitting-lampu.jpg', 0);

-- 3. Tabel Testimoni Pelanggan
CREATE TABLE testimoni (
    id_testimoni INT AUTO_INCREMENT PRIMARY KEY,
    nama_pelanggan VARCHAR(100) NOT NULL,
    rating INT DEFAULT 5,
    komentar TEXT NOT NULL
);

INSERT INTO testimoni (nama_pelanggan, rating, komentar) VALUES
('Budi Santoso', 5, 'Pelayanan ramah, harga bahan bangunan bersaing dan pengiriman sangat cepat sampai lokasi proyek.'),
('Sari Dewi', 5, 'Belanja kebutuhan renovasi rumah jadi praktis, barang lengkap dan kualitasnya terjamin.'),
('Andi Pratama', 5, 'Admin responsif saat ditanya stok barang. Sangat direkomendasikan untuk belanja grosir maupun eceran.');

-- 4. Tabel Pesan Kontak
CREATE TABLE pesan_kontak (
    id_pesan INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    pesan TEXT NOT NULL,
    tanggal_kirim TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. Tabel Users (Pelanggan & Admin)
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'pelanggan') DEFAULT 'pelanggan'
);