CREATE DATABASE IF NOT EXISTS bmkg_gorontalo;
USE bmkg_gorontalo;

-- Tabel Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    role ENUM('admin_meteorologi', 'admin_klimatologi', 'admin_geofisika', 'superadmin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (username, password, nama, role) VALUES
('admin_meteo', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1fS3Ier4E3O1b.y0R4fA4yP8B1l9v/6', 'Admin Meteorologi', 'admin_meteorologi'),
('admin_klima', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1fS3Ier4E3O1b.y0R4fA4yP8B1l9v/6', 'Admin Klimatologi', 'admin_klimatologi'),
('admin_geo', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1fS3Ier4E3O1b.y0R4fA4yP8B1l9v/6', 'Admin Geofisika', 'admin_geofisika')
ON DUPLICATE KEY UPDATE username=username;

-- Tabel Berita Admin
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
('Peringatan Dini Cuaca Ekstrem Wilayah Gorontalo', 'Meteorologi', 'Stasiun Meteorologi Gorontalo mengimbau masyarakat untuk waspada terhadap potensi hujan lebat disertai angin kencang di wilayah Kota Gorontalo, Bone Bolango, dan Gorontalo Utara.', 'https://images.unsplash.com/photo-1516834474-48c0abc2a902?w=800', 'Admin Meteorologi'),
('Analisis Potensi Gelombang Tinggi di Perairan Teluk Tomini', 'Meteorologi', 'Prakiraan tinggi gelombang di perairan Teluk Tomini diperkirakan mencapai 1.25 hingga 2.0 meter. Dihimbau nelayan tradisional tetap waspada.', 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800', 'Admin Meteorologi');

-- Tabel Gempa
CREATE TABLE IF NOT EXISTS gempa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal_jam DATETIME NOT NULL,
    magnitudo DECIMAL(3,1) NOT NULL,
    kedalaman VARCHAR(20) NOT NULL,
    latitude DECIMAL(9,6) NOT NULL,
    longitude DECIMAL(9,6) NOT NULL,
    lokasi TEXT NOT NULL,
    potensi VARCHAR(255) NOT NULL,
    dirasakan VARCHAR(255),
    kategori ENUM('terkini', 'diatas_5mb') DEFAULT 'terkini',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO gempa (tanggal_jam, magnitudo, kedalaman, latitude, longitude, lokasi, potensi, dirasakan, kategori) VALUES
('2026-10-07 08:15:00', 5.2, '10 km', 0.123400, 123.056700, '75 km Tenggara Bone Bolango, Gorontalo', 'Tidak berpotensi Tsunami', 'III MMI Gorontalo', 'diatas_5mb');

-- Tabel Cuaca Per Jam Kecamatan
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
('Kota Gorontalo', 'Kota Selatan', '06:00', 25, 'Cerah', 85, 5),
('Kota Gorontalo', 'Kota Selatan', '09:00', 29, 'Cerah Berawan', 75, 10),
('Kota Gorontalo', 'Kota Selatan', '12:00', 32, 'Hujan Ringan', 68, 15),
('Kota Gorontalo', 'Kota Selatan', '15:00', 30, 'Hujan Sedang', 72, 12),
('Kota Gorontalo', 'Kota Selatan', '18:00', 27, 'Berawan', 80, 8),
('Kota Gorontalo', 'Kota Selatan', '21:00', 26, 'Cerah Berawan', 82, 6),

('Kab. Gorontalo', 'Limboto', '06:00', 24, 'Cerah Berawan', 88, 4),
('Kab. Gorontalo', 'Limboto', '09:00', 28, 'Cerah', 78, 8),
('Kab. Gorontalo', 'Limboto', '12:00', 31, 'Berawan', 70, 12),
('Kab. Gorontalo', 'Limboto', '15:00', 29, 'Hujan Ringan', 76, 10),
('Kab. Gorontalo', 'Limboto', '18:00', 26, 'Berawan', 84, 5),
('Kab. Gorontalo', 'Limboto', '21:00', 25, 'Cerah Berawan', 85, 4),

('Kab. Boalemo', 'Tilamuta', '12:00', 33, 'Cerah', 65, 18),
('Kab. Pohuwato', 'Marisa', '12:00', 34, 'Cerah', 60, 20);
