<?php
session_start();
require_once '../koneksi.php';

// Cek Login Admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$admin_user = $_SESSION['admin_username'] ?? 'admin';
$admin_role = strtolower($_SESSION['admin_role'] ?? 'all');

$page_title = "Panel Admin BMKG Gorontalo";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }
        .sidebar { min-height: 100vh; background: #0f172a; color: #fff; }
        .sidebar .nav-link { color: #94a3b8; font-weight: 500; padding: 12px 20px; border-radius: 8px; margin-bottom: 4px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #0284c7; color: #fff; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        
        <!-- SIDEBAR PANEL ADMIN -->
        <div class="col-md-3 col-lg-2 sidebar p-3 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center gap-2 mb-4 px-2 pt-2">
                    <img src="https://www.bmkg.go.id/asset/img/logo/logo-bmkg.png" height="36" alt="BMKG">
                    <div>
                        <h6 class="fw-bold mb-0 text-white">PANEL ADMIN</h6>
                        <small class="text-info" style="font-size: 0.72rem;"><?= strtoupper($admin_user) ?></small>
                    </div>
                </div>

                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="berita_kelola.php"><i class="bi bi-newspaper me-2"></i> Kelola Berita</a>
                    </li>

                    <!-- MENU KLIMATOLOGI (Dapat diakses oleh admin_klima, klimatologi, admin_klimatologi, dan all) -->
                    <?php if (strpos($admin_role, 'klima') !== false || $admin_role === 'all'): ?>
                        <li class="nav-item">
                            <a class="nav-link text-info fw-semibold" href="iklim_kelola.php">
                                <i class="bi bi-calendar3 me-2"></i> Analisis Iklim Semester
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- MENU METEOROLOGI -->
                    <!-- MENU METEOROLOGI: SIARAN PERS -->
                    <?php if (strpos($admin_role, 'meteo') !== false || $admin_role === 'all'): ?>
                        <li class="nav-item">
                            <a class="nav-link fw-semibold" href="pers.php">
                                <i class="bi bi-file-earmark-text-fill me-2"></i> Siaran Pers Meteo
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-semibold" href="penerbangan.php">
                                <i class="bi bi-airplane me-2"></i> Cuaca Penerbangan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-semibold" href="maritim.php">
                                <i class="bi bi-tsunami me-2"></i> Cuacan Pelabuhan
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- MENU GEOFISIKA -->
                    <?php if (strpos($admin_role, 'geo') !== false || $admin_role === 'all'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="gempa_kelola.php"><i class="bi bi-activity me-2"></i> Monitoring Gempa</a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item mt-3">
                        <a class="nav-link text-warning" href="change_password.php"><i class="bi bi-key me-2"></i> Ubah Password</a>
                    </li>
                </ul>
            </div>

            <!-- TOMBOL LOGOUT SIDEBAR BAWAH -->
            <div class="pt-3 border-top border-secondary">
                <a class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2" href="logout.php">
                    <i class="bi bi-box-arrow-right"></i> Keluar / Logout
                </a>
            </div>
        </div>

        <!-- MAIN CONTENT AREA -->
        <div class="col-md-9 col-lg-10 p-4">
            
            <!-- TOPBAR DASHBOARD UTAMA -->
            <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-3 shadow-sm border">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary-subtle text-primary p-2.5 rounded-circle fs-4">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0">Selamat Datang, <?= htmlspecialchars($admin_user) ?></h6>
                        <small class="text-muted">Hak Akses: <span class="badge bg-primary text-white"><?= strtoupper($_SESSION['admin_role'] ?? 'ADMIN') ?></span></small>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="../index.php" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="bi bi-globe me-1"></i> Lihat Web Portal
                    </a>
                    <a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </a>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3 border-start border-4 border-primary">
                        <h6 class="text-muted small mb-1">Total Berita Diterbitkan</h6>
                        <h3 class="fw-bold mb-0">
                            <?php 
                            try {
                                $stmt = $pdo->query("SELECT COUNT(*) FROM berita");
                                echo $stmt->fetchColumn();
                            } catch (PDOException $e) {
                                echo "0";
                            }
                            ?>
                        </h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-3 border-start border-4 border-success">
                        <h6 class="text-muted small mb-1">Stasiun Aktif</h6>
                        <h3 class="fw-bold mb-0">3 Stasiun</h3>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>