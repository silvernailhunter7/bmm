<?php
session_start();
if (!isset($_SESSION['role'])) { header("Location: ../login.php"); exit; }
if ($_SESSION['role'] !== 'admin_meteorologi' && $_SESSION['role'] !== 'superadmin') { http_response_code(403); die("Akses Ditolak: Modul Meteorologi."); }
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Kelola Cuaca Pelabuhan - Admin</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="p-4"><div class="container">
    <h2>Kelola Data Cuaca Pelabuhan Gorontalo</h2>
    <a href="index.php" class="btn btn-secondary my-3">&laquo; Kembali ke Dashboard</a>
    <p class="alert alert-info">Form kelola data Prakiraan Gelombang Pelabuhan & Teluk Tomini.</p>
</div></body></html>