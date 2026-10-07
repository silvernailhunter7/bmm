<?php
session_start();
if (!isset($_SESSION['role'])) { header("Location: ../login.php"); exit; }
if ($_SESSION['role'] !== 'admin_meteorologi' && $_SESSION['role'] !== 'superadmin') { http_response_code(403); die("Akses Ditolak: Modul Meteorologi."); }
require_once '../koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Kelola Cuaca Harian - Admin</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="p-4"><div class="container">
    <h2>Kelola Data Cuaca Harian Kecamatan</h2>
    <a href="index.php" class="btn btn-secondary my-3">&laquo; Kembali ke Dashboard</a>
    <div class="card p-4 shadow-sm">
        <h5>Tambah / Edit Data Cuaca Kecamatan</h5>
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-4"><label>Kabupaten</label><input type="text" name="kabupaten" class="form-control" required></div>
                <div class="col-md-4"><label>Kecamatan</label><input type="text" name="kecamatan" class="form-control" required></div>
                <div class="col-md-4"><label>Suhu (°C)</label><input type="number" name="suhu" class="form-control" required></div>
                <div class="col-md-4"><label>Kelembapan (%)</label><input type="number" name="kelembapan" class="form-control" required></div>
                <div class="col-md-4"><label>Kondisi</label><input type="text" name="kondisi" class="form-control" placeholder="Cerah/Hujan" required></div>
                <div class="col-md-4"><label>Kecepatan Angin (km/j)</label><input type="number" name="angin" class="form-control" required></div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Simpan Data</button>
        </form>
    </div>
</div></body></html>