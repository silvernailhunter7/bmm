<?php
require_once '../koneksi.php';
$query = mysqli_query($koneksi, "SELECT * FROM gempa WHERE kategori='diatas_5mb' ORDER BY tanggal_jam DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Gempa > 5.0 M - BMKG Gorontalo</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light"><div class="container my-4">
<a href="../index.php" class="btn btn-secondary mb-3">&laquo; Kembali ke Beranda</a>
<h3>Daftar Gempa Bumi Magnitudo > 5.0 M</h3>
<div class="bg-white p-3 shadow-sm rounded">
    <table class="table table-bordered">
        <thead class="table-warning">
            <tr><th>Waktu (WITA)</th><th>Magnitudo</th><th>Kedalaman</th><th>Lokasi</th><th>Potensi</th><th>Dirasakan</th></tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($query)): ?>
            <tr>
                <td><?= date('d-m-Y H:i', strtotime($row['tanggal_jam'])) ?></td>
                <td><strong class="text-danger"><?= htmlspecialchars($row['magnitudo'], ENT_QUOTES, 'UTF-8') ?> M</strong></td>
                <td><?= htmlspecialchars($row['kedalaman'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['lokasi'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['potensi'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($row['dirasakan'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</div></body></html>