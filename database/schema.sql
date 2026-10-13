-- ============================================
-- Database Inventaris - Tugas Rutin 8
-- 3 tabel: kategori, supplier, produk (+ Foreign Key)
-- ============================================
CREATE DATABASE IF NOT EXISTS inventaris_db
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventaris_db;

DROP TABLE IF EXISTS produk;
DROP TABLE IF EXISTS kategori;
DROP TABLE IF EXISTS supplier;

CREATE TABLE kategori (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE,
    deskripsi     VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE supplier (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(100) NOT NULL,
    telepon       VARCHAR(20)  NOT NULL,
    alamat        VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE produk (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_produk VARCHAR(20)  NOT NULL UNIQUE,
    nama_produk VARCHAR(150) NOT NULL,
    kategori_id INT UNSIGNED NOT NULL,
    supplier_id INT UNSIGNED NOT NULL,
    harga       DECIMAL(12,0) NOT NULL,
    stok        INT UNSIGNED NOT NULL DEFAULT 0,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_produk_kategori FOREIGN KEY (kategori_id)
        REFERENCES kategori(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_produk_supplier FOREIGN KEY (supplier_id)
        REFERENCES supplier(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------- Data seed (5 data per tabel) ----------
INSERT INTO kategori (nama_kategori, deskripsi) VALUES
('Elektronik', 'Perangkat elektronik dan aksesorisnya'),
('Alat Tulis', 'Perlengkapan menulis dan kantor'),
('Furnitur', 'Meja, kursi, dan perabot'),
('Makanan & Minuman', 'Produk konsumsi kemasan'),
('Kebersihan', 'Perlengkapan kebersihan');

INSERT INTO supplier (nama_supplier, telepon, alamat) VALUES
('PT Maju Elektronik', '081234567801', 'Jl. Merdeka No. 10'),
('CV Sumber Rejeki', '081234567802', 'Jl. Sudirman No. 25'),
('UD Berkah Abadi', '081234567803', 'Jl. Gatot Subroto No. 7'),
('PT Nusantara Furnitur', '081234567804', 'Jl. Pemuda No. 18'),
('CV Sinar Terang', '081234567805', 'Jl. Diponegoro No. 3');

INSERT INTO produk (kode_produk, nama_produk, kategori_id, supplier_id, harga, stok) VALUES
('ELK-001', 'Laptop Asus Vivobook 14', 1, 1, 8500000, 12),
('ATK-001', 'Pulpen Gel Hitam (Box 12)', 2, 2, 36000, 150),
('FRN-001', 'Meja Kerja Minimalis', 3, 4, 1250000, 8),
('MNM-001', 'Kopi Instan Sachet (Dus)', 4, 3, 48000, 3),
('KBS-001', 'Sabun Cuci Piring 800ml', 5, 5, 18500, 45);