CREATE DATABASE IF NOT EXISTS bmkg_gto;
USE bmkg_gto;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    role ENUM('admin_meteorologi', 'admin_klimatologi', 'admin_geofisika', 'superadmin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

TRUNCATE TABLE users;
INSERT INTO users (username, password, nama, role) VALUES
('admin_meteo', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1fS3Ier4E3O1b.y0R4fA4yP8B1l9v/6', 'Admin Meteorologi', 'admin_meteorologi'),
('admin_klimat', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1fS3Ier4E3O1b.y0R4fA4yP8B1l9v/6', 'Admin Klimatologi', 'admin_klimatologi'),
('admin_geo', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1fS3Ier4E3O1b.y0R4fA4yP8B1l9v/6', 'Admin Geofisika', 'admin_geofisika');

CREATE TABLE IF NOT EXISTS berita (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    isi TEXT NOT NULL,
    gambar VARCHAR(255) DEFAULT 'default_news.jpg',
    penulis VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO berita (judul, kategori, isi, gambar, penulis) VALUES
('Peringatan Dini Cuaca Ekstrem Wilayah Gorontalo', 'Meteorologi', 'Stasiun Meteorologi Gorontalo mengimbau masyarakat untuk waspada terhadap potensi hujan lebat.', 'https://images.unsplash.com/photo-1516834474-48c0abc2a902?w=800', 'Admin Meteorologi'),
('Analisis Potensi Gelombang Tinggi di Perairan Teluk Tomini', 'Meteorologi', 'Prakiraan tinggi gelombang di perairan Teluk Tomini diperkirakan mencapai 1.25 hingga 2.0 meter.', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800', 'Admin Meteorologi');

CREATE TABLE IF NOT EXISTS cuaca_perjam (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kabupaten VARCHAR(100) NOT NULL,
    kecamatan VARCHAR(100) NOT NULL,
    jam VARCHAR(10) NOT NULL,
    suhu INT NOT NULL,
    kondisi VARCHAR(50) NOT NULL,
    kelembapan INT NOT NULL,
    kecepatan_angin INT NOT NULL
);

INSERT INTO cuaca_perjam (kabupaten, kecamatan, jam, suhu, kondisi, kelembapan, kecepatan_angin) VALUES
('Kota Gorontalo', 'Kota Selatan', '12:00', 32, 'Hujan Ringan', 68, 15),
('Kota Gorontalo', 'Kota Utara', '12:00', 32, 'Cerah Berawan', 65, 12),
('Kota Gorontalo', 'Kota Barat', '12:00', 31, 'Hujan Ringan', 70, 14),
('Kota Gorontalo', 'Kota Timur', '12:00', 32, 'Berawan', 66, 13),
('Kota Gorontalo', 'Kota Tengah', '12:00', 32, 'Cerah Berawan', 64, 15),
('Kota Gorontalo', 'Dumbo Raya', '12:00', 33, 'Cerah Berawan', 60, 16),
('Kota Gorontalo', 'Hulonthalangi', '12:00', 33, 'Cerah', 58, 18),
('Kota Gorontalo', 'Dungingi', '12:00', 31, 'Hujan Ringan', 72, 10),
('Kota Gorontalo', 'Sipatanah', '12:00', 32, 'Berawan', 68, 12),
('Kab. Gorontalo', 'Limboto', '12:00', 31, 'Berawan', 70, 12),
('Kab. Gorontalo', 'Limboto Barat', '12:00', 31, 'Berawan', 71, 11),
('Kab. Gorontalo', 'Telaga', '12:00', 32, 'Hujan Sedang', 75, 14),
('Kab. Gorontalo', 'Telaga Biru', '12:00', 31, 'Hujan Ringan', 73, 13),
('Kab. Gorontalo', 'Telaga Jaya', '12:00', 32, 'Hujan Ringan', 70, 12),
('Kab. Gorontalo', 'Batudaa', '12:00', 32, 'Cerah Berawan', 65, 15),
('Kab. Gorontalo', 'Batudaa Pantai', '12:00', 33, 'Cerah', 62, 20),
('Kab. Gorontalo', 'Bongomeme', '12:00', 30, 'Hujan Sedang', 78, 10),
('Kab. Gorontalo', 'Dungaliyo', '12:00', 30, 'Berawan', 74, 9),
('Kab. Gorontalo', 'Tibawa', '12:00', 31, 'Hujan Ringan', 72, 11),
('Kab. Gorontalo', 'Pulubala', '12:00', 32, 'Cerah Berawan', 66, 14),
('Kab. Gorontalo', 'Boliyohuto', '12:00', 30, 'Hujan Sedang', 76, 10),
('Kab. Gorontalo', 'Mootilango', '12:00', 31, 'Berawan', 70, 12),
('Kab. Gorontalo', 'Tolangohula', '12:00', 31, 'Hujan Ringan', 73, 11),
('Kab. Gorontalo', 'Asparaga', '12:00', 30, 'Hujan Sedang', 79, 8),
('Kab. Gorontalo', 'Bilato', '12:00', 31, 'Berawan', 72, 10),
('Kab. Gorontalo', 'Biluhu', '12:00', 32, 'Cerah', 64, 18),
('Kab. Gorontalo', 'Tabongo', '12:00', 31, 'Hujan Ringan', 71, 12),
('Kab. Bone Bolango', 'Suwawa', '12:00', 30, 'Hujan Petir', 80, 12),
('Kab. Bone Bolango', 'Suwawa Timur', '12:00', 29, 'Hujan Petir', 82, 10),
('Kab. Bone Bolango', 'Suwawa Selatan', '12:00', 30, 'Hujan Sedang', 78, 11),
('Kab. Bone Bolango', 'Suwawa Tengah', '12:00', 30, 'Hujan Sedang', 79, 10),
('Kab. Bone Bolango', 'Kabila', '12:00', 31, 'Hujan Ringan', 74, 13),
('Kab. Bone Bolango', 'Kabila Bone', '12:00', 32, 'Cerah Berawan', 66, 17),
('Kab. Bone Bolango', 'Botupingge', '12:00', 31, 'Hujan Ringan', 73, 12),
('Kab. Bone Bolango', 'Tilongkabila', '12:00', 31, 'Berawan', 70, 13),
('Kab. Bone Bolango', 'Tapa', '12:00', 31, 'Hujan Ringan', 72, 12),
('Kab. Bone Bolango', 'Bulango Selatan', '12:00', 31, 'Berawan', 69, 14),
('Kab. Bone Bolango', 'Bulango Timur', '12:00', 31, 'Hujan Ringan', 71, 13),
('Kab. Bone Bolango', 'Bulango Utara', '12:00', 29, 'Hujan Sedang', 80, 9),
('Kab. Bone Bolango', 'Bulango Ulu', '12:00', 28, 'Hujan Sedang', 83, 8),
('Kab. Bone Bolango', 'Bone', '12:00', 32, 'Cerah', 63, 19),
('Kab. Bone Bolango', 'Bonepantai', '12:00', 32, 'Cerah Berawan', 65, 18),
('Kab. Bone Bolango', 'Bonedaa', '12:00', 32, 'Cerah', 62, 20),
('Kab. Bone Bolango', 'Bulawa', '12:00', 32, 'Cerah Berawan', 64, 17),
('Kab. Bone Bolango', 'Pinogu', '12:00', 27, 'Hujan Petir', 85, 6),
('Kab. Gorontalo Utara', 'Kwandang', '12:00', 33, 'Cerah Berawan', 62, 18),
('Kab. Gorontalo Utara', 'Atinggola', '12:00', 32, 'Cerah', 64, 19),
('Kab. Gorontalo Utara', 'Sumalata', '12:00', 33, 'Cerah', 61, 20),
('Kab. Gorontalo Utara', 'Sumalata Timur', '12:00', 32, 'Cerah Berawan', 63, 18),
('Kab. Gorontalo Utara', 'Anggrek', '12:00', 33, 'Cerah Berawan', 62, 17),
('Kab. Gorontalo Utara', 'Monano', '12:00', 32, 'Berawan', 66, 16),
('Kab. Gorontalo Utara', 'Tolinggula', '12:00', 31, 'Hujan Ringan', 70, 14),
('Kab. Gorontalo Utara', 'Biau', '12:00', 31, 'Hujan Ringan', 72, 13),
('Kab. Gorontalo Utara', 'Tomilito', '12:00', 33, 'Cerah', 60, 19),
('Kab. Gorontalo Utara', 'Gentuma Raya', '12:00', 32, 'Cerah Berawan', 65, 17),
('Kab. Gorontalo Utara', 'Ponelo Kepulauan', '12:00', 33, 'Cerah', 59, 21),
('Kab. Boalemo', 'Tilamuta', '12:00', 33, 'Cerah', 65, 18),
('Kab. Boalemo', 'Dulupi', '12:00', 32, 'Cerah Berawan', 64, 16),
('Kab. Boalemo', 'Mananggu', '12:00', 32, 'Berawan', 67, 15),
('Kab. Boalemo', 'Botumoito', '12:00', 33, 'Cerah', 61, 19),
('Kab. Boalemo', 'Wonsoari', '12:00', 31, 'Hujan Ringan', 73, 12),
('Kab. Boalemo', 'Paguyaman', '12:00', 31, 'Hujan Ringan', 71, 13),
('Kab. Boalemo', 'Paguyaman Pantai', '12:00', 33, 'Cerah', 60, 20),
('Kab. Pohuwato', 'Marisa', '12:00', 34, 'Cerah', 60, 20),
('Kab. Pohuwato', 'Buntulia', '12:00', 33, 'Cerah Berawan', 62, 18),
('Kab. Pohuwato', 'Duhiadaa', '12:00', 33, 'Cerah', 61, 19),
('Kab. Pohuwato', 'Patilanggio', '12:00', 33, 'Cerah Berawan', 63, 17),
('Kab. Pohuwato', 'Randangan', '12:00', 33, 'Berawan', 65, 16),
('Kab. Pohuwato', 'Taluditi', '12:00', 31, 'Hujan Sedang', 76, 11),
('Kab. Pohuwato', 'Dengilo', '12:00', 32, 'Hujan Ringan', 70, 14),
('Kab. Pohuwato', 'Paguat', '12:00', 33, 'Cerah', 61, 19),
('Kab. Pohuwato', 'Popayato', '12:00', 33, 'Cerah Berawan', 63, 18),
('Kab. Pohuwato', 'Popayato Barat', '12:00', 33, 'Cerah', 60, 20),
('Kab. Pohuwato', 'Popayato Timur', '12:00', 32, 'Cerah Berawan', 64, 17),
('Kab. Pohuwato', 'Lemito', '12:00', 33, 'Cerah', 61, 19),
('Kab. Pohuwato', 'Wanggarasi', '12:00', 32, 'Berawan', 67, 15);


CREATE TABLE IF NOT EXISTS iklim (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    deskripsi TEXT NOT NULL,
    gambar VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO iklim (id, judul, deskripsi, gambar) VALUES
(1, 'Analisis & Prakiraan Musim Hujan Semester II - 2026', 'Semester II - 2026: Puncak musim hujan diperkirakan terjadi pada bulan November - Desember dengan curah hujan rata-rata berkisar antara 200 - 350 mm/bulan.', 'https://data.bmkg.go.id/datamkg/MEWS/Klimatologi/Prakiraan_Hujan_Bulanan.png')
ON DUPLICATE KEY UPDATE id=id;


CREATE TABLE IF NOT EXISTS cuaca_10harian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    deskripsi TEXT NOT NULL,
    gambar VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO cuaca_10harian (id, judul, deskripsi, gambar) VALUES
(1, 'Prakiraan Hujan Dasarian (10 Harian) Gorontalo', 'Informasi akumulasi curah hujan dan sifat hujan per 10 hari untuk bidang pertanian dan keilmuan klimatologi.', 'https://data.bmkg.go.id/datamkg/MEWS/Klimatologi/Prakiraan_Hujan_Dasarian.png')
ON DUPLICATE KEY UPDATE id=id;