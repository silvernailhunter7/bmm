CREATE DATABASE IF NOT EXISTS bmkg_gorontalo;
USE bmkg_gorontalo;

-- Tabel Users / Admin
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
('2026-10-07 08:15:00', 5.2, '10 km', 0.123400, 123.056700, '75 km Tenggara Bone Bolango, Gorontalo', 'Tidak berpotensi Tsunami', 'III MMI Gorontalo', 'diatas_5mb'),
('2026-10-07 05:30:00', 3.8, '15 km', 0.543200, 122.987600, '12 km Barat Daya Kota Gorontalo', 'Tidak berpotensi Tsunami', 'II MMI Kota Gorontalo', 'terkini');

-- Tabel Cuaca Kecamatan
CREATE TABLE IF NOT EXISTS cuaca_kecamatan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kecamatan VARCHAR(100) NOT NULL,
    kabupaten VARCHAR(100) NOT NULL,
    suhu INT NOT NULL,
    kelembapan INT NOT NULL,
    kondisi VARCHAR(50) NOT NULL,
    kecepatan_angin INT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO cuaca_kecamatan (kecamatan, kabupaten, suhu, kelembapan, kondisi, kecepatan_angin) VALUES
('Kota Selatan', 'Kota Gorontalo', 31, 75, 'Cerah Berawan', 12),
('Dungingi', 'Kota Gorontalo', 30, 78, 'Hujan Ringan', 10),
('Limboto', 'Kab. Gorontalo', 29, 80, 'Berawan', 8),
('Tilamuta', 'Kab. Boalemo', 32, 70, 'Cerah', 15),
('Marisa', 'Kab. Pohuwato', 33, 68, 'Cerah', 18),
('Kwandang', 'Kab. Gorontalo Utara', 30, 77, 'Hujan Sedang', 14),
('Suwawa', 'Kab. Bone Bolango', 28, 85, 'Hujan Ringan', 6);
