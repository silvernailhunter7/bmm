<?php
$host = "127.0.0.1";
$user = "root";
$pass = "";
$db   = "bmkg_gto";

if (function_exists('mysqli_connect')) {
    $koneksi = @mysqli_connect($host, $user, $pass, $db);
}

if (!isset($koneksi) || !$koneksi) {
    try {
        $db_file = __DIR__ . '/bmkg_gto.db';
        $pdo = new PDO("sqlite:" . $db_file);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT UNIQUE, password TEXT, nama TEXT, role TEXT);
            CREATE TABLE IF NOT EXISTS berita (id INTEGER PRIMARY KEY AUTOINCREMENT, judul TEXT, kategori TEXT, isi TEXT, gambar TEXT, penulis TEXT, created_at DATETIME DEFAULT CURRENT_TIMESTAMP);
            CREATE TABLE IF NOT EXISTS cuaca_perjam (id INTEGER PRIMARY KEY AUTOINCREMENT, kabupaten TEXT, kecamatan TEXT, jam TEXT, suhu INTEGER, kondisi TEXT, kelembapan INTEGER, kecepatan_angin INTEGER);
        ");

        if ($pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() == 0) {
            $pdo->exec("INSERT INTO users (username, password, nama, role) VALUES 
                ('admin_meteo', '\$2y\$10\$e0MYzXyjpJS7Pd0RVvHwHe1fS3Ier4E3O1b.y0R4fA4yP8B1l9v/6', 'Admin Meteorologi', 'admin_meteorologi'),
                ('admin_klima', '\$2y\$10\$e0MYzXyjpJS7Pd0RVvHwHe1fS3Ier4E3O1b.y0R4fA4yP8B1l9v/6', 'Admin Klimatologi', 'admin_klimatologi'),
                ('admin_geo', '\$2y\$10\$e0MYzXyjpJS7Pd0RVvHwHe1fS3Ier4E3O1b.y0R4fA4yP8B1l9v/6', 'Admin Geofisika', 'admin_geofisika')");
            $pdo->exec("INSERT INTO berita (judul, kategori, isi, gambar, penulis) VALUES 
                ('Peringatan Dini Cuaca Ekstrem Wilayah Gorontalo', 'Meteorologi', 'Stasiun Meteorologi Gorontalo mengimbau masyarakat untuk waspada.', 'https://images.unsplash.com/photo-1516834474-48c0abc2a902?w=800', 'Admin Meteorologi')");
            $pdo->exec("INSERT INTO cuaca_perjam (kabupaten, kecamatan, jam, suhu, kondisi, kelembapan, kecepatan_angin) VALUES 
                ('Kota Gorontalo', 'Kota Selatan', '12:00', 32, 'Hujan Ringan', 68, 15),
                ('Kab. Gorontalo', 'Limboto', '12:00', 31, 'Berawan', 70, 12)");
        }
    } catch (PDOException $e) {
        die("Koneksi Database Gagal: " . $e->getMessage());
    }
}
?>