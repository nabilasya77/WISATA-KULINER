-- =========================================================
-- Database: wonogiri_wisata
-- Sistem Informasi Kuliner Lokal & Destinasi Wisata Wonogiri
-- Updated: Data Lengkap 2025
-- =========================================================

CREATE DATABASE IF NOT EXISTS wonogiri_wisata CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE wonogiri_wisata;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS kategori_kuliner (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS kategori_wisata (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS kuliner (
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

CREATE TABLE IF NOT EXISTS destinasi_wisata (
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

CREATE TABLE IF NOT EXISTS galeri_kuliner (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kuliner_id INT NOT NULL,
    url_gambar VARCHAR(255) NOT NULL,
    FOREIGN KEY (kuliner_id) REFERENCES kuliner(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS galeri_destinasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destinasi_id INT NOT NULL,
    url_gambar VARCHAR(255) NOT NULL,
    FOREIGN KEY (destinasi_id) REFERENCES destinasi_wisata(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS ulasan (
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

CREATE TABLE IF NOT EXISTS buku_tamu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_pengunjung VARCHAR(100) NOT NULL,
    asal_kota VARCHAR(100) NOT NULL,
    tujuan_kunjungan VARCHAR(150) NOT NULL,
    kesan_pesan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_bayar DECIMAL(10,2) NOT NULL,
    status_pembayaran ENUM('pending','sukses','batal') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS detail_transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaksi_id INT NOT NULL,
    kuliner_id INT NOT NULL,
    jumlah INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE,
    FOREIGN KEY (kuliner_id) REFERENCES kuliner(id)
);

-- =========================================================
-- SEED DATA
-- =========================================================

INSERT INTO kategori_kuliner (nama_kategori) VALUES
('Makanan Berat'), ('Jajanan & Camilan'), ('Minuman'), ('Oleh-oleh');

INSERT INTO kategori_wisata (nama_kategori) VALUES
('Alam & Pegunungan'), ('Waduk & Air'), ('Air Terjun'), ('Wisata Budaya & Religi'), ('Goa');

-- =========================================================
-- DATA KULINER WONOGIRI (12 data lengkap)
-- =========================================================
INSERT INTO kuliner (nama_kuliner, slug, kategori_id, deskripsi, harga_mulai, lokasi, alamat_lengkap, latitude, longitude, jam_operasional, kontak, gambar_utama, rating, jumlah_ulasan, is_unggulan) VALUES
('Nasi Tiwul', 'nasi-tiwul', 1, 'Makanan pokok ikonik Wonogiri dari tepung gaplek (singkong kering) yang dikukus hingga mengembang. Teksturnya legit, sedikit kenyal, dan beraroma khas. Disajikan hangat dengan sayur lodeh, urap, atau lauk pauk tradisional. Simbol ketangguhan masyarakat Wonogiri dan kini jadi daya tarik wisata kuliner.', 8000, 'Kecamatan Wonogiri', 'Pasar Kota Wonogiri, Jl. Diponegoro, Wonogiri', -7.8146000, 110.9216000, '06:00 - 20:00', '0812-3456-7890', 'https://images.unsplash.com/photo-1586190848861-99aa4a171e90?q=80&w=800', 4.8, 215, 1),
('Tiwul Goreng', 'tiwul-goreng', 1, 'Kreasi modern dari tiwul yang digoreng dengan bumbu rempah pilihan. Hasilnya gurih, renyah di luar dan lembut di dalam. Cocok sebagai camilan sore atau pendamping kopi. Banyak dijual di warung tradisional dan pasar Wonogiri.', 7000, 'Kecamatan Wuryantoro', 'Pasar Wuryantoro, Wonogiri', -7.8580000, 110.7980000, '07:00 - 18:00', '0813-2345-6789', 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=800', 4.5, 98, 1),
('Geti Wijen', 'geti-wijen', 2, 'Oleh-oleh khas Wonogiri yang wajib dibawa! Terbuat dari biji wijen yang disangrai lalu dicetak bersama gula merah cair hingga membentuk batangan manis-legit. Kaya serat dan nutrisi, camilan ini telah ada sejak generasi ke generasi sebagai kebanggaan produk UMKM lokal.', 10000, 'Kecamatan Wonogiri', 'Sentra Oleh-oleh Jl. Ahmad Yani, Wonogiri', -7.8160000, 110.9230000, '08:00 - 21:00', '0815-3456-7890', 'https://images.unsplash.com/photo-1601055903647-ddf1ee9701b7?q=80&w=800', 4.7, 167, 1),
('Kacang Mete Wonogiri', 'kacang-mete', 4, 'Wonogiri adalah salah satu penghasil jambu mete terbesar di Jawa Tengah. Kacang mete lokal diolah dengan teknik sangrai tradisional menggunakan wajan tanah liat hingga menghasilkan cita rasa gurih, renyah, dan aroma yang menggoda. Tersedia varian original, pedas, dan balado.', 25000, 'Kecamatan Jatisrono', 'Sentra Mete Jatisrono, Wonogiri', -7.7280000, 111.1380000, '08:00 - 17:00', '0816-4567-8901', 'https://images.unsplash.com/photo-1599599810769-bcde5a160d32?q=80&w=800', 4.9, 312, 1),
('Es Gempol Pleret', 'es-gempol-pleret', 3, 'Minuman segar tradisional yang unik! Gempol adalah bola-bola dari tepung beras putih, sedangkan pleret adalah lempengan tipis merah. Disajikan dalam kuah santan manis dengan gula jawa cair dan es serut. Sangat menyegarkan di siang hari yang terik.', 6000, 'Kecamatan Wonogiri', 'Alun-alun Kota Wonogiri', -7.8155000, 110.9220000, '10:00 - 22:00', '0817-5678-9012', 'https://images.unsplash.com/photo-1551024601-bec78aea704b?q=80&w=800', 4.4, 89, 0),
('Sate Kere', 'sate-kere', 1, 'Kuliner legendaris khas Wonogiri! Kere berarti miskin dalam bahasa Jawa, namun rasanya luar biasa. Dibuat dari tempe gembus, jeroan sapi, dan kulit sapi yang ditusuk dan dibakar, kemudian disiram bumbu kacang kaya rempah yang menggugah selera.', 12000, 'Kecamatan Wonogiri', 'Jl. Diponegoro No. 12, Wonogiri', -7.8130000, 110.9200000, '17:00 - 23:00', '0818-6789-0123', 'https://images.unsplash.com/photo-1529563021893-cc83c992d75d?q=80&w=800', 4.6, 143, 1),
('Growol', 'growol', 2, 'Makanan fermentasi tradisional dari singkong yang difermentasi 2-3 hari hingga menghasilkan aroma asam yang khas. Teksturnya kenyal padat dengan cita rasa unik yang tidak ditemukan di daerah lain. Produk autentik kearifan lokal Wonogiri.', 5000, 'Kecamatan Eromoko', 'Pasar Eromoko, Wonogiri', -7.9120000, 110.7780000, '06:00 - 17:00', '0819-7890-1234', 'https://images.unsplash.com/photo-1506368249639-73a05d6f6488?q=80&w=800', 4.2, 56, 0),
('Madu Klanceng', 'madu-klanceng', 4, 'Madu premium dari lebah klanceng (Trigona sp.) yang diternakkan di hutan-hutan Wonogiri. Berbeda dari madu biasa, rasanya asam-manis khas dengan kandungan antioksidan jauh lebih tinggi. Dipercaya berkhasiat untuk daya tahan tubuh dan kesehatan.', 35000, 'Kecamatan Girimarto', 'Desa Girimarto, Wonogiri', -7.7150000, 111.0450000, '08:00 - 17:00', '0821-8901-2345', 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?q=80&w=800', 4.9, 198, 1),
('Bakmi Jowo Wonogiri', 'bakmi-jowo-wonogiri', 1, 'Mie telur buatan tangan yang dimasak di atas bara arang dengan wajan tradisional. Proses lambat menghasilkan cita rasa smoky yang autentik. Disajikan dengan potongan ayam kampung, telur, sayuran segar, dan kuah kaldu gurih. Ikon kuliner malam Wonogiri yang wajib dicoba.', 15000, 'Kecamatan Wonogiri', 'Jl. Pemuda, Wonogiri', -7.8140000, 110.9210000, '17:00 - 24:00', '0822-9012-3456', 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?q=80&w=800', 4.7, 234, 1),
('Keripik Tempe Benguk', 'keripik-tempe-benguk', 2, 'Camilan super renyah dari kacang benguk yang difermentasi menjadi tempe lalu diiris tipis dan digoreng kering. Rasanya gurih unik. Salah satu oleh-oleh khas yang paling banyak dicari wisatawan karena tahan lama dan harganya terjangkau.', 12000, 'Kecamatan Wonogiri', 'Pasar Wonogiri, Wonogiri', -7.8148000, 110.9218000, '07:00 - 18:00', '0823-0123-4567', 'https://images.unsplash.com/photo-1621996346565-e3dbc353d2e5?q=80&w=800', 4.5, 112, 0),
('Wedang Uwuh', 'wedang-uwuh', 3, 'Minuman herbal tradisional yang hangat dan berkhasiat. Dibuat dari aneka rempah seperti kayu manis, cengkeh, jahe, kapulaga, dan daun pandan yang direbus bersama. Berwarna merah cantik, rasanya manis-pedas-hangat yang memanjakan tenggorokan.', 8000, 'Kecamatan Wonogiri', 'Warung Tradisional Alun-alun Wonogiri', -7.8152000, 110.9215000, '16:00 - 22:00', '0824-1234-5678', 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?q=80&w=800', 4.6, 77, 0),
('Pecel Wonogiri', 'pecel-wonogiri', 1, 'Pecel khas Wonogiri menggunakan bumbu kacang yang lebih kental dan legit dengan campuran daun jeruk purut yang wangi. Disajikan di atas daun pisang dengan lauk rempeyek kacang, telur pindang, dan nasi hangat. Sarapan favorit warga lokal setiap pagi.', 10000, 'Kecamatan Wonogiri', 'Pasar Wonogiri & Warung sekitar kota', -7.8145000, 110.9205000, '06:00 - 11:00', '0825-2345-6789', 'https://images.unsplash.com/photo-1512058564366-18510be2db19?q=80&w=800', 4.8, 189, 1);

-- =========================================================
-- DATA DESTINASI WISATA WONOGIRI (12 data lengkap)
-- =========================================================
INSERT INTO destinasi_wisata (nama_destinasi, slug, kategori_id, deskripsi, tiket_masuk, lokasi, alamat_lengkap, latitude, longitude, jam_operasional, kontak, gambar_utama, rating, jumlah_ulasan, fasilitas, is_unggulan) VALUES
('Waduk Gajah Mungkur', 'waduk-gajah-mungkur', 2, 'Ikon wisata utama Wonogiri! Waduk buatan terbesar di Jawa Tengah dengan luas 8.800 ha yang dibangun pada era 1970-an. Menawarkan panorama air biru luas dikelilingi bukit-bukit hijau. Tersedia wisata perahu, memancing, camping, dan area kuliner di tepi waduk. Sunset di sini sangat legendaris.', 15000, 'Kecamatan Wonogiri', 'Sendang, Wonogiri, Jawa Tengah', -7.8470000, 110.8650000, '07:00 - 17:00', '0812-WGM-1234', 'https://images.unsplash.com/photo-1439066615861-d1af74d74000?q=80&w=800', 4.8, 542, 'Parkir Luas, Warung Makan, Toilet, Gazebo, Sewa Perahu, Area Camping, Spot Foto', 1),
('Bukit Cumbri', 'bukit-cumbri', 1, 'Spot sunrise dan sunset terbaik di Wonogiri! Bukit berbatu dengan ketinggian 700 mdpl ini menyuguhkan panorama 360 derajat berupa hamparan Waduk Gajah Mungkur dan Gunung Lawu di kejauhan. Jalur pendakian ringan 30 menit. Sangat cocok untuk photography dan camping.', 10000, 'Kecamatan Wonogiri', 'Desa Sendang, Kec. Wonogiri', -7.8390000, 110.8720000, '05:00 - 18:00', '0813-CBR-2345', 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?q=80&w=800', 4.9, 387, 'Basecamp, Toilet, Warung, Area Camping, Gardu Pandang, Spot Foto Instagramable', 1),
('Air Terjun Girimanik', 'air-terjun-girimanik', 3, 'Surga tersembunyi di lereng Gunung Lawu! Kompleks tiga air terjun bertingkat di kawasan hutan pinus yang sejuk dengan suhu 18-22 derajat Celsius. Suara gemericik air dan kicauan burung menciptakan harmoni alam yang menenangkan jiwa. Cocok untuk wisata keluarga dan pecinta alam.', 12000, 'Kecamatan Slogohimo', 'Desa Setren, Slogohimo, Wonogiri', -7.7340000, 111.2080000, '07:00 - 17:00', '0814-GRM-3456', 'https://images.unsplash.com/photo-1432405972618-c60b0225b8f9?q=80&w=800', 4.9, 298, 'Parkir, Toilet, Mushola, Warung, Area Piknik, Jembatan Gantung, Jalur Trekking', 1),
('Gunung Gandul', 'gunung-gandul', 1, 'Bukit kapur ikonik dengan puncak terbuka menyerupai padang savana mini berwarna hijau segar. Spot foto favorit para instagramer dengan latar belakang Waduk Gajah Mungkur yang cantik. Sering disebut Little New Zealand-nya Wonogiri karena pemandangannya yang indah.', 7000, 'Kecamatan Wonogiri', 'Desa Sendang, Wonogiri', -7.8410000, 110.8700000, '06:00 - 18:00', '0815-GGL-4567', 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=800', 4.7, 256, 'Parkir, Warung, Toilet, Spot Foto, Jalur Trekking Ringan, Gardu Pandang', 1),
('Pantai Sembukan', 'pantai-sembukan', 2, 'Pantai selatan Wonogiri yang eksotis dan masih terjaga kealamiannya. Ombak besar Samudera Hindia memukul tebing-tebing karang yang megah. Dikenal sebagai tempat ritual spiritual Malam 1 Suro. Pemandangan matahari terbenam di sini luar biasa romantis.', 5000, 'Kecamatan Paranggupito', 'Desa Sembukan, Paranggupito, Wonogiri', -8.2080000, 110.9530000, '06:00 - 18:00', '0816-SMK-5678', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?q=80&w=800', 4.5, 178, 'Parkir, Warung Ikan Bakar, Toilet, Area Piknik, Spot Foto Tebing', 1),
('Goa Putri Kencana', 'goa-putri-kencana', 5, 'Goa alam yang menakjubkan di kawasan karst Wonogiri dengan formasi stalaktit dan stalagmit terbentuk selama jutaan tahun. Dilengkapi pencahayaan warna-warni dan jalur yang aman, menjadikannya wisata edukasi geologi yang menarik untuk semua usia.', 8000, 'Kecamatan Pracimantoro', 'Desa Pracimantoro, Wonogiri', -8.0450000, 110.8910000, '08:00 - 16:00', '0817-GPK-6789', 'https://images.unsplash.com/photo-1534274988757-a28bf1a57c17?q=80&w=800', 4.5, 134, 'Parkir, Penerangan Goa, Pemandu Wisata, Toilet, Warung', 0),
('Air Terjun Sumber Pitu', 'air-terjun-sumber-pitu', 3, 'Air terjun dengan tujuh aliran air yang mengalir bersamaan dari tebing batu setinggi 25 meter. Pitu dalam bahasa Jawa berarti tujuh. Dikelilingi hutan tropis lebat yang masih alami. Suasananya magis dan memukau, cocok untuk meditasi dan recharge energi.', 10000, 'Kecamatan Karangtengah', 'Desa Karangtengah, Wonogiri', -7.9870000, 110.9340000, '07:00 - 17:00', '0818-SBP-7890', 'https://images.unsplash.com/photo-1467890947394-8171244e5b8d?q=80&w=800', 4.7, 112, 'Parkir, Toilet, Warung, Jalur Trekking, Area Bermain Air', 0),
('Museum Wayang Indonesia', 'museum-wayang', 4, 'Museum yang menyimpan lebih dari 5.000 koleksi wayang dari seluruh penjuru Nusantara, menjadikannya salah satu museum wayang terlengkap di Indonesia. Terdapat wayang kulit, wayang golek, wayang beber, hingga wayang dari luar negeri. Pertunjukan wayang setiap akhir pekan.', 5000, 'Kecamatan Wonogiri', 'Jl. Pemuda No. 5, Wonogiri', -7.8125000, 110.9195000, '08:00 - 16:00 (Tutup Senin)', '0819-MSW-8901', 'https://images.unsplash.com/photo-1582034986517-30d163aa1da9?q=80&w=800', 4.4, 98, 'AC, Toilet, Pemandu, Toko Souvenir, Ruang Pertunjukan', 0),
('Kahyangan Argopuro', 'kahyangan-argopuro', 4, 'Tempat petilasan dan meditasi spiritual yang sakral di lereng Gunung Lawu. Menurut kepercayaan Jawa, tempat ini pernah digunakan Raja-raja Mataram untuk bertapa. Dikelilingi hutan pinus tua yang mistis dengan kabut pagi yang menyelimuti. Pengalaman spiritual dan budaya yang tak terlupakan.', 5000, 'Kecamatan Jatisrono', 'Lereng Gunung Lawu, Wonogiri', -7.7200000, 111.1650000, '06:00 - 17:00', '0820-KAP-9012', 'https://images.unsplash.com/photo-1542401886-65d6c61db217?q=80&w=800', 4.6, 87, 'Parkir, Area Meditasi, Toilet, Penginapan Sederhana', 0),
('Telaga Ngebel Mini', 'telaga-ngebel-mini', 2, 'Danau alami kecil yang jernih dikelilingi pohon-pohon rindang di kawasan perbukitan Wonogiri. Air tenangnya memantulkan refleksi langit dan pepohonan seperti cermin raksasa. Tempat favorit untuk memancing, piknik keluarga, dan jalan pagi yang menyegarkan.', 3000, 'Kecamatan Sidoharjo', 'Sidoharjo, Wonogiri', -7.7650000, 110.9870000, '06:00 - 18:00', '0821-TLG-0123', 'https://images.unsplash.com/photo-1501854140801-50d01698950b?q=80&w=800', 4.3, 65, 'Parkir, Sewa Alat Pancing, Warung, Gazebo, Area Piknik', 0),
('Puncak Joglo', 'puncak-joglo', 1, 'Titik tertinggi kawasan Gunung Lawu dengan panorama luar biasa. Di hari cerah, bisa melihat deretan gunung seperti Merapi, Merbabu, Lawu, dan Semeru di kejauhan. Trek menantang namun terbayar lunas dengan pemandangan spektakuler. Favorit pendaki dari Solo, Sragen, dan Wonogiri.', 15000, 'Kecamatan Jatisrono', 'Lereng Lawu, Jatisrono, Wonogiri', -7.6870000, 111.1920000, '24 Jam (Camping)', '0822-PJG-1234', 'https://images.unsplash.com/photo-1486870591958-9b9d0d1dda99?q=80&w=800', 4.8, 321, 'Basecamp, Toilet, Warung, Area Camping, Pemandu Pendakian', 1),
('Desa Wisata Kepuhsari', 'desa-wisata-kepuhsari', 4, 'Desa penghasil wayang kulit terbesar di Indonesia! Hampir seluruh warga adalah pengrajin wayang kulit yang mewarisi keahlian turun-temurun. Wisatawan dapat langsung belajar membuat wayang dan memesan wayang custom. Pengalaman budaya yang autentik dan langka di era modern ini.', 10000, 'Kecamatan Manyaran', 'Desa Kepuhsari, Manyaran, Wonogiri', -7.8750000, 110.7350000, '08:00 - 16:00', '0823-DWK-2345', 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?q=80&w=800', 4.7, 145, 'Workshop Membuat Wayang, Galeri, Homestay, Pemandu, Toko Souvenir', 1);

-- =========================================================
-- SAMPLE ULASAN
-- =========================================================
INSERT INTO ulasan (user_id, tipe, item_id, nama_pengulas, rating, komentar) VALUES
(NULL, 'kuliner', 1, 'Budi Santoso', 5, 'Nasi tiwul di sini autentik banget! Bawa anak-anak ke sini biar kenal kuliner nenek moyang.'),
(NULL, 'kuliner', 4, 'Sari Dewi', 5, 'Kacang mete Wonogiri memang beda, gurihnya alami dan renyah. Beli 5 bungkus langsung habis!'),
(NULL, 'kuliner', 9, 'Rahmat Hidayat', 5, 'Bakmi jowo dimasak arang memang beda rasanya, smoky banget. Antre 1 jam tapi worth it!'),
(NULL, 'destinasi', 1, 'Andi Prasetyo', 5, 'Sunset di Waduk Gajah Mungkur luar biasa indah. Wajib datang!'),
(NULL, 'destinasi', 2, 'Rina Kusuma', 5, 'Bukit Cumbri sunrise-nya bikin merinding keindahannya. Tracking 30 menit worth it banget!'),
(NULL, 'destinasi', 3, 'Dian Pramudita', 5, 'Air Terjun Girimanik sejuk banget, anak-anak senang main air. Fasilitasnya juga bagus.'),
(NULL, 'destinasi', 12, 'Mega Lestari', 5, 'Desa Kepuhsari luar biasa! Bisa lihat langsung cara membuat wayang. Edukasi budaya terbaik!');

-- =========================================================
-- SAMPLE BUKU TAMU
-- =========================================================
INSERT INTO buku_tamu (nama_pengunjung, asal_kota, tujuan_kunjungan, kesan_pesan) VALUES
('Ahmad Fauzi', 'Surakarta', 'Wisata Alam', 'Wonogiri luar biasa! Waduk Gajah Mungkur sangat indah, akan kembali lagi membawa keluarga.'),
('Dewi Ratna', 'Yogyakarta', 'Wisata Kuliner', 'Nasi tiwul dan kacang mete Wonogiri terbaik! Oleh-olehnya lengkap dan terjangkau.'),
('Hendra Gunawan', 'Semarang', 'Perjalanan Bisnis', 'Terima kasih atas sambutan hangat warga Wonogiri. Kota yang bersih dan nyaman!');

-- =========================================================
-- CATATAN SETELAH IMPORT
-- =========================================================
-- 1. Buka register.php dan daftar akun (role default = user)
-- 2. Jalankan query ini di phpMyAdmin untuk menaikkan role jadi admin:
--    UPDATE users SET role = 'admin' WHERE username = 'USERNAME_KAMU';
-- 3. Gambar menggunakan Unsplash CDN - pastikan ada koneksi internet
