<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: ../login.php");
    exit;
}

$role = $_SESSION['role'];
$nama = $_SESSION['nama'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Kontrol Admin - BMKG Gorontalo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand">Dashboard Admin (<?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>)</span>
    <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
</nav>

<div class="container my-4">
    <h3>Selamat Datang di Panel Kelola Data BMKG Gorontalo</h3>
    <p class="text-muted">Role Anda: <strong><?= htmlspecialchars(strtoupper(str_replace('_', ' ', $role)), ENT_QUOTES, 'UTF-8') ?></strong></p>

    <div class="row g-3 mt-2">
        <div class="col-md-4">
            <div class="card <?= ($role == 'admin_meteorologi' || $role == 'superadmin') ? 'border-primary' : 'bg-light opacity-50' ?>">
                <div class="card-header bg-primary text-white">Kelola Meteorologi</div>
                <div class="card-body">
                    <?php if ($role == 'admin_meteorologi' || $role == 'superadmin'): ?>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><a href="cuaca_harian.php">Cuaca Harian</a></li>
                            <li class="list-group-item"><a href="cuaca_bandara.php">Cuaca Bandara</a></li>
                            <li class="list-group-item"><a href="cuaca_pelabuhan.php">Cuaca Pelabuhan</a></li>
                            <li class="list-group-item"><a href="satelit.php">Update Satelit</a></li>
                            <li class="list-group-item"><a href="radar.php">Radar Cuaca</a></li>
                        </ul>
                    <?php else: ?>
                        <p class="text-danger small mb-0">Akses Dibatasi. Hanya Admin Meteorologi.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card <?= ($role == 'admin_klimatologi' || $role == 'superadmin') ? 'border-success' : 'bg-light opacity-50' ?>">
                <div class="card-header bg-success text-white">Kelola Klimatologi</div>
                <div class="card-body">
                    <?php if ($role == 'admin_klimatologi' || $role == 'superadmin'): ?>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><a href="iklim_semester.php">Iklim Per Semester</a></li>
                            <li class="list-group-item"><a href="cuaca_10harian.php">Cuaca Per 10 Harian</a></li>
                        </ul>
                    <?php else: ?>
                        <p class="text-danger small mb-0">Akses Dibatasi. Hanya Admin Klimatologi.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card <?= ($role == 'admin_geofisika' || $role == 'superadmin') ? 'border-warning' : 'bg-light opacity-50' ?>">
                <div class="card-header bg-warning text-dark">Kelola Geofisika</div>
                <div class="card-body">
                    <?php if ($role == 'admin_geofisika' || $role == 'superadmin'): ?>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><a href="gempa_terkini.php">Gempa Terkini</a></li>
                            <li class="list-group-item"><a href="gempa_5mb.php">Gempa > 5.0 M</a></li>
                        </ul>
                    <?php else: ?>
                        <p class="text-danger small mb-0">Akses Dibatasi. Hanya Admin Geofisika.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>