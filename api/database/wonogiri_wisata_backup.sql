-- =========================================================
-- Database: wonogiri_wisata
-- Sistem Informasi Kuliner Lokal & Destinasi Wisata Wonogiri
-- =========================================================

CREATE DATABASE IF NOT EXISTS wonogiri_wisata CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE wonogiri_wisata;

-- ---------------------------------------------------------
-- Tabel: users (dipakai login.php, register.php, admin)
-- ---------------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------
-- Tabel: kategori_kuliner
-- ---------------------------------------------------------
CREATE TABLE kategori_kuliner (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(50) NOT NULL
);

-- ---------------------------------------------------------
-- Tabel: kategori_wisata
-- ---------------------------------------------------------
CREATE TABLE kategori_wisata (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(50) NOT NULL
);

-- ---------------------------------------------------------
-- Tabel: kuliner
-- ---------------------------------------------------------
CREATE TABLE kuliner (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kuliner VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    kategori_id INT,
    deskripsi TEXT,
    harga_mulai DECIMAL(10,2) DEFAULT 0,
    lokasi VARCHAR(150),
    alamat_lengkap VARCHAR(255),
    latitude DECIMAL(10,7) DEFAULT NULL,
    longitude DECIMAL(10,7) DEFAULT NULL,
    jam_operasional VARCHAR(100),
    kontak VARCHAR(50),
    gambar_utama VARCHAR(255),
    rating DECIMAL(2,1) DEFAULT 0,
    jumlah_ulasan INT DEFAULT 0,
    is_unggulan TINYINT(1) DEFAULT 0,
    status ENUM('aktif','nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori_kuliner(id) ON DELETE SET NULL
);

-- ---------------------------------------------------------
-- Tabel: destinasi_wisata
-- ---------------------------------------------------------
CREATE TABLE destinasi_wisata (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_destinasi VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    kategori_id INT,
    deskripsi TEXT,
    tiket_masuk DECIMAL(10,2) DEFAULT 0,
    lokasi VARCHAR(150),
    alamat_lengkap VARCHAR(255),
    latitude DECIMAL(10,7) DEFAULT NULL,
    longitude DECIMAL(10,7) DEFAULT NULL,
    jam_operasional VARCHAR(100),
    kontak VARCHAR(50),
    gambar_utama VARCHAR(255),
    rating DECIMAL(2,1) DEFAULT 0,
    jumlah_ulasan INT DEFAULT 0,
    fasilitas TEXT,
    is_unggulan TINYINT(1) DEFAULT 0,
    status ENUM('aktif','nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori_wisata(id) ON DELETE SET NULL
);

-- ---------------------------------------------------------
-- Tabel: galeri_kuliner (multi gambar per kuliner)
-- ---------------------------------------------------------
CREATE TABLE galeri_kuliner (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kuliner_id INT NOT NULL,
    url_gambar VARCHAR(255) NOT NULL,
    FOREIGN KEY (kuliner_id) REFERENCES kuliner(id) ON DELETE CASCADE
);

-- ---------------------------------------------------------
-- Tabel: galeri_destinasi (multi gambar per destinasi)
-- ---------------------------------------------------------
CREATE TABLE galeri_destinasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destinasi_id INT NOT NULL,
    url_gambar VARCHAR(255) NOT NULL,
    FOREIGN KEY (destinasi_id) REFERENCES destinasi_wisata(id) ON DELETE CASCADE
);

-- ---------------------------------------------------------
-- Tabel: ulasan (untuk kuliner & destinasi sekaligus)
-- user_id boleh NULL kalau ulasan ditulis tanpa login
-- ---------------------------------------------------------
CREATE TABLE ulasan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    tipe ENUM('kuliner','destinasi') NOT NULL,
    item_id INT NOT NULL,
    nama_pengulas VARCHAR(100) NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    komentar TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ---------------------------------------------------------
-- Tabel: buku_tamu (dipakai buku_tamu.php & admin/buku_tamu.php)
-- ---------------------------------------------------------
CREATE TABLE buku_tamu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pengunjung VARCHAR(100) NOT NULL,
    asal_kota VARCHAR(100) NOT NULL,
    tujuan_kunjungan VARCHAR(150) NOT NULL,
    kesan_pesan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------
-- Tabel: transaksi (dipakai admin/transaksi.php)
-- ---------------------------------------------------------
CREATE TABLE transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_bayar DECIMAL(10,2) NOT NULL,
    status_pembayaran ENUM('pending','sukses','batal') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ---------------------------------------------------------
-- Tabel: detail_transaksi (rincian item per transaksi)
-- ---------------------------------------------------------
CREATE TABLE detail_transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaksi_id INT NOT NULL,
    kuliner_id INT NOT NULL,
    jumlah INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE,
    FOREIGN KEY (kuliner_id) REFERENCES kuliner(id)
);

-- =========================================================
-- DATA AWAL (SEED DATA)
-- =========================================================

INSERT INTO kategori_kuliner (nama_kategori) VALUES
('Makanan Berat'), ('Jajanan & Camilan'), ('Minuman'), ('Oleh-oleh');

INSERT INTO kategori_wisata (nama_kategori) VALUES
('Alam & Pegunungan'), ('Waduk & Air'), ('Air Terjun'), ('Wisata Budaya & Religi'), ('Goa');

INSERT INTO kuliner
(nama_kuliner, slug, kategori_id, deskripsi, harga_mulai, lokasi, alamat_lengkap, latitude, longitude, jam_operasional, kontak, gambar_utama, rating, jumlah_ulasan, is_unggulan)
VALUES
('Nasi Tiwul', 'nasi-tiwul', 1, 'Makanan khas dari tepung singkong/gaplek yang menjadi makanan pokok masyarakat Wonogiri sejak dahulu, biasa disajikan dengan sayur dan lauk tradisional.', 8000, 'Kecamatan Wonogiri', 'Pasar Kota Wonogiri, Wonogiri', -7.8146000, 110.9216000, '06:00 - 20:00', '0812xxxxxxx', 'https://images.unsplash.com/photo-1604908554007-39b7747a5dfa?q=80&w=800', 4.7, 128, 1),
('Tiwul Goreng', 'tiwul-goreng', 1, 'Olahan tiwul yang digoreng dengan bumbu rempah, gurih dan renyah, cocok sebagai camilan maupun pengganti nasi.', 7000, 'Kecamatan Wuryantoro', 'Wuryantoro, Wonogiri', -7.8580000, 110.7980000, '07:00 - 18:00', '0813xxxxxxx', 'https://images.unsplash.com/photo-1599921841143-819065a55cc6?q=80&w=800', 4.5, 76, 1),
('Geti Wijen', 'geti-wijen', 2, 'Camilan manis legit berbahan dasar wijen dan gula merah, oleh-oleh khas yang wajib dibawa dari Wonogiri.', 10000, 'Kecamatan Wonogiri', 'Sentra Oleh-oleh Wonogiri', -7.8160000, 110.9230000, '08:00 - 21:00', '0815xxxxxxx', 'https://images.unsplash.com/photo-1606312619070-d48b4c652a52?q=80&w=800', 4.6, 95, 1),
('Kacang Mete Wonogiri', 'kacang-mete', 4, 'Kacang mete berkualitas hasil pertanian lokal, diolah menjadi camilan gurih dan renyah, populer sebagai oleh-oleh.', 25000, 'Kecamatan Jatisrono', 'Jatisrono, Wonogiri', -7.7280000, 111.1380000, '08:00 - 17:00', '0816xxxxxxx', 'https://images.unsplash.com/photo-1605478547929-93f0c52624db?q=80&w=800', 4.8, 142, 1),
('Es Gempol Pleret', 'es-gempol-pleret', 3, 'Minuman segar dari tepung beras berbentuk bulat (gempol) dan pipih (pleret) disajikan dengan santan dan gula jawa cair.', 6000, 'Kecamatan Wonogiri', 'Alun-alun Wonogiri', -7.8155000, 110.9220000, '10:00 - 22:00', '0817xxxxxxx', 'https://images.unsplash.com/photo-1551024601-bec78aea704b?q=80&w=800', 4.4, 60, 0),
('Sate Kere', 'sate-kere', 1, 'Sate dengan bahan utama tempe gembus dan jeroan sapi, disiram bumbu kacang, menjadi kuliner legendaris murah dan nikmat.', 12000, 'Kecamatan Wonogiri', 'Jl. Diponegoro, Wonogiri', -7.8130000, 110.9200000, '17:00 - 23:00', '0818xxxxxxx', 'https://images.unsplash.com/photo-1529563021893-cc83c992d75d?q=80&w=800', 4.6, 88, 0),
('Growol', 'growol', 2, 'Makanan fermentasi dari singkong, bertekstur kenyal dan beraroma khas, biasa dimakan sebagai camilan pendamping.', 5000, 'Kecamatan Eromoko', 'Eromoko, Wonogiri', -7.9120000, 110.7780000, '06:00 - 17:00', '0819xxxxxxx', 'https://images.unsplash.com/photo-1559054663-e8d23213f55b?q=80&w=800', 4.2, 34, 0),
('Madu Klanceng', 'madu-klanceng', 4, 'Madu murni dari lebah klanceng (trigona) khas peternakan lokal Wonogiri, dipercaya berkhasiat tinggi untuk kesehatan.', 35000, 'Kecamatan Girimarto', 'Girimarto, Wonogiri', -7.7150000, 111.0450000, '08:00 - 17:00', '0821xxxxxxx', 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?q=80&w=800', 4.9, 110, 1);

INSERT INTO destinasi_wisata
(nama_destinasi, slug, kategori_id, deskripsi, tiket_masuk, lokasi, alamat_lengkap, latitude, longitude, jam_operasional, kontak, gambar_utama, rating, jumlah_ulasan, fasilitas, is_unggulan)
VALUES
('Bukit Cumbri', 'bukit-cumbri', 1, 'Spot wisata alam dengan gardu pandang di atas tebing, menawarkan panorama Waduk Gajah Mungkur dan perbukitan hijau yang memukau saat matahari terbit maupun terbenam.', 10000, 'Kecamatan Wonogiri', 'Desa Sendang, Wonogiri', -7.8390000, 110.8720000, '06:00 - 18:00', '0822xxxxxxx', 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?q=80&w=800', 4.8, 215, 1),
('Waduk Gajah Mungkur', 'waduk-gajah-mungkur', 2, 'Waduk terbesar di Wonogiri yang menjadi ikon wisata, menyuguhkan pemandangan air yang luas dikelilingi pegunungan, cocok untuk wisata perahu dan memancing.', 15000, 'Kecamatan Wonogiri', 'Sendang, Wonogiri', -7.8470000, 110.8650000, '07:00 - 17:00', '0823xxxxxxx', 'https://images.unsplash.com/photo-1439066615861-d1af74d74000?q=80&w=800', 4.7, 340, 1),
('Air Terjun Girimanik', 'air-terjun-girimanik', 3, 'Kompleks tiga air terjun bertingkat di lereng Gunung Lawu dengan suasana hutan asri dan udara sejuk, cocok untuk wisata keluarga.', 12000, 'Kecamatan Slogohimo', 'Setren, Slogohimo, Wonogiri', -7.7340000, 111.2080000, '07:00 - 17:00', '0824xxxxxxx', 'https://images.unsplash.com/photo-1432405972618-c60b0225b8f9?q=80&w=800', 4.9, 198, 1),
('Goa Putri Kencana', 'goa-putri-kencana', 5, 'Goa alami dengan stalaktit dan stalagmit indah, dilengkapi penerangan dan jalur yang aman untuk dijelajahi wisatawan.', 8000, 'Kecamatan Pracimantoro', 'Pracimantoro, Wonogiri', -8.0450000, 110.8910000, '08:00 - 16:00', '0825xxxxxxx', 'https://images.unsplash.com/photo-1534274988757-a28bf1a57c17?q=80&w=800', 4.5, 87, 0),
('Pantai Sembukan', 'pantai-sembukan', 2, 'Pantai selatan dengan ombak besar dan pemandangan tebing karang, dikenal juga dengan ritual budaya pesisir selatan Jawa.', 5000, 'Kecamatan Paranggupito', 'Paranggupito, Wonogiri', -8.2080000, 110.9530000, '06:00 - 18:00', '0826xxxxxxx', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=800', 4.4, 102, 0),
('Museum Wayang Indonesia', 'museum-wayang', 4, 'Museum yang menyimpan ribuan koleksi wayang dari berbagai daerah di Indonesia, menjadi pusat edukasi budaya bagi pelajar dan wisatawan.', 5000, 'Kecamatan Wonogiri', 'Jl. Pemuda, Wonogiri', -7.8125000, 110.9195000, '08:00 - 16:00', '0827xxxxxxx', 'https://images.unsplash.com/photo-1582034986517-30d163aa1da9?q=80&w=800', 4.3, 64, 0),
('Gunung Gandul', 'gunung-gandul', 1, 'Bukit kapur dengan jalur trekking ringan dan spot foto ikonik berupa hamparan rumput hijau menyerupai padang savana mini.', 7000, 'Kecamatan Wonogiri', 'Desa Sendang, Wonogiri', -7.8410000, 110.8700000, '06:00 - 18:00', '0828xxxxxxx', 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=800', 4.6, 150, 1),
('Air Terjun Sumber Pitu', 'air-terjun-sumber-pitu', 3, 'Air terjun tersembunyi dengan tujuh aliran air yang jatuh dari tebing, dikelilingi vegetasi hutan tropis yang masih alami.', 10000, 'Kecamatan Karangtengah', 'Karangtengah, Wonogiri', -7.9870000, 110.9340000, '07:00 - 17:00', '0829xxxxxxx', 'https://images.unsplash.com/photo-1467890947394-8171244e5b8d?q=80&w=800', 4.7, 73, 0);

-- =========================================================
-- CATATAN SETELAH IMPORT
-- =========================================================
-- Belum ada akun admin di tabel `users` karena password harus di-hash
-- dengan PHP password_hash() (bukan teks biasa).
-- Cara membuat akun admin pertama:
--   1. Buka register.php dan daftar akun seperti biasa (role default = 'user').
--   2. Jalankan query ini di phpMyAdmin untuk menaikkan role jadi admin:
--      UPDATE users SET role = 'admin' WHERE username = 'USERNAME_KAMU';
