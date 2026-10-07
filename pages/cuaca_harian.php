<?php
require_once '../koneksi.php';
$query = mysqli_query($koneksi, "SELECT * FROM cuaca_kecamatan ORDER BY kabupaten, kecamatan ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Cuaca Harian Gorontalo - BMKG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-4">
    <a href="../index.php" class="btn btn-secondary mb-3">&laquo; Kembali ke Beranda</a>
    <h3 class="fw-bold mb-3">Informasi Cuaca Harian Kabupaten/Kota Gorontalo</h3>
    <div class="table-responsive bg-white shadow-sm p-3 rounded">
        <table class="table table-bordered table-striped">
            <thead class="table-primary">
                <tr><th>Kabupaten/Kota</th><th>Kecamatan</th><th>Suhu (°C)</th><th>Kelembapan (%)</th><th>Kondisi</th><th>Kecepatan Angin (km/j)</th></tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($query)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['kabupaten'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['kecamatan'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int)$row['suhu'] ?> °C</td>
                    <td><?= (int)$row['kelembapan'] ?> %</td>
                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['kondisi'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td><?= (int)$row['kecepatan_angin'] ?> km/j</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>