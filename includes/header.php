<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_subpage = $is_subpage ?? false;
$base_url = $is_subpage ? '../' : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'BMKG Provinsi Gorontalo' ?></title>
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .topbar-bmkg {
            background-color: #0f172a;
            color: #94a3b8;
            font-size: 0.8rem;
            padding: 6px 0;
        }
        .navbar-juanda {
            background: #ffffff;
            border-bottom: 3px solid #0284c7;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .navbar-brand-text .title {
            font-weight: 800;
            font-size: 1.1rem;
            color: #0f172a;
            letter-spacing: -0.3px;
            line-height: 1.2;
        }
        .navbar-brand-text .subtitle {
            font-size: 0.72rem;
            color: #0284c7;
            font-weight: 600;
            text-transform: uppercase;
        }
        .nav-link {
            font-weight: 700;
            font-size: 0.85rem;
            color: #334155 !important;
            padding: 12px 12px !important;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            transition: all 0.2s ease;
        }
        .nav-link:hover, .nav-link.active {
            color: #0284c7 !important;
        }

        /* HOVER DROPDOWN AUTOMATIC */
        @media (min-width: 992px) {
            .navbar-nav .dropdown:hover .dropdown-menu {
                display: block;
                margin-top: 0;
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
                transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            }
            .navbar-nav .dropdown .dropdown-menu {
                display: block;
                opacity: 0;
                visibility: hidden;
                transform: translateY(10px);
                transition: all 0.2s ease;
            }
        }
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.12);
            border-radius: 16px;
            padding: 8px 0;
            border-top: 3px solid #0284c7;
            font-size: 0.88rem;
        }
        .dropdown-item {
            font-weight: 500;
            padding: 9px 20px;
            color: #334155;
            transition: all 0.15s ease;
        }
        .dropdown-item:hover {
            background-color: #f0f9ff;
            color: #0284c7;
            padding-left: 24px;
        }
    </style>
</head>
<body>

<!-- TOPBAR CONTACT & TIME -->
<div class="topbar-bmkg d-none d-md-block">
    <div class="container-fluid px-lg-5 d-flex justify-content-between align-items-center">
        <div>
            <span><i class="bi bi-envelope-fill text-info me-1"></i> stamet.gorontalo@bmkg.go.id</span>
            <span><i class="bi bi-envelope-fill text-info me-1"></i> stageof.gorontalo@bmkg.go.id</span>
            <span><i class="bi bi-envelope-fill text-info me-1"></i> staklim.gorontalo@bmkg.go.id</span>
        </div>
        <div>
            <span class="me-3"><i class="bi bi-clock-fill text-info me-1"></i> <span id="clock-wita">--:--:--</span> WITA</span>
            <a href="<?= $base_url ?>admin/login.php" class="text-white text-decoration-none bg-primary px-2.5 py-1 rounded small fw-bold">
                <i class="bi bi-lock-fill me-1"></i> Login Admin
            </a>
        </div>
    </div>
</div>

<!-- MAIN NAVBAR JUANDA STYLE -->
<nav class="navbar navbar-expand-xl navbar-juanda sticky-top">
    <div class="container-fluid px-lg-5">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= $base_url ?>index.php">
            <img src="https://www.bmkg.go.id/asset/img/logo/logo-bmkg.png" alt="Logo BMKG" height="46">
            <div class="navbar-brand-text">
                <div class="title">BMKG GORONTALO</div>
            </div>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarJuanda">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarJuanda">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-xl-center">
                <li class="nav-item">
                    <a class="nav-link active" href="<?= $base_url ?>index.php">BERANDA</a>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">PROFIL</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= $base_url ?>pages/visi.php">Visi dan Misi</a></li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>pages/tugas.php">Tugas dan Fungsi</a></li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>pages/organisasi.php"">Struktur Organisasi</a></li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>pages/sejarah.php">Sejarah</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">BERITA</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= $base_url ?>pages/berita.php">Berita Terbaru</a></li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>pages/siaran_pers.php">Siaran Pers</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">PENGAMATAN</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= $base_url ?>pages/citra_satelit.php">Citra Satelit Himawari-9</a></li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>pages/radar_cuaca.php">Radar Cuaca Gorontalo</a></li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>pages/pos_hujan.php">Data Pos Hujan</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">PRAKIRAAN</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= $base_url ?>pages/cuaca_harian.php">Cuaca Kab, Kec & Desa</a></li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>pages/cuaca_penerbangan.php">Cuaca Bandara</a></li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>pages/cuaca_pelabuhan.php">Cuaca Maritim</a></li>
                        <li><a class="dropdown-item d-flex justify-content-between align-items-center" href="<?= $base_url ?>pages/iklim_semester.php">IKLIM </a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">GEMPA BUMI</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= $base_url ?>pages/gempa_terkini.php">Peta Interaktif Gempa</a></li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>pages/gempa_5mb.php">Daftar Gempa M &ge; 5.0</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">PELAYANAN DATA</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= $base_url ?>pages/ptsp.php">Alur Pelayanan Data (PTSP)</a></li>
                        <li><a class="dropdown-item" href="#">Permohonan Informasi Cuaca</a></li>
                        <li><a class="dropdown-item" href="<?= $base_url ?>pages/pnbp.php">Tarif PNBP Data BMKG</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">PUBLIK</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Survei IKM & IPK</a></li>
                        <li><a class="dropdown-item" href="#">Hasil IKM dan IPK</a></li>
                        <li><a class="dropdown-item" href="#">Kritik dan Saran</a></li>
                        <li><a class="dropdown-item" href="#">Pengaduan</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">PENERBANGAN</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item d-flex justify-content-between align-items-center" href="https://web-aviation.bmkg.go.id/web/metar_speci.php">METAR & TAF <i class="bi bi-box-arrow-up-right ms-2 text-muted" style="font-size:0.75rem;"></i></a></li>
                        <li><a class="dropdown-item d-flex justify-content-between align-items-center" href="https://web-aviation.bmkg.go.id/maps" target="_blank">Flight Maps <i class="bi bi-box-arrow-up-right ms-2 text-muted" style="font-size:0.75rem;"></i></a></li>
                        <li><a class="dropdown-item d-flex justify-content-between align-items-center" href="https://aviation.bmkg.go.id" target="_blank">Flight Document <i class="bi bi-box-arrow-up-right ms-2 text-muted" style="font-size:0.75rem;"></i></a></li>
                        <li><a class="dropdown-item d-flex justify-content-between align-items-center" href="https://inasiam.bmkg.go.id/?zoom=6.48&lat=0.7935&lng=122.5037" target="_blank">INA-SIAM <i class="bi bi-box-arrow-up-right ms-2 text-muted" style="font-size:0.75rem;"></i></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    function updateClock() {
        const now = new Date();
        const options = { timeZone: 'Asia/Makassar', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
        const timeStr = new Intl.DateTimeFormat('id-ID', options).format(now);
        const clockEl = document.getElementById('clock-wita');
        if (clockEl) clockEl.innerText = timeStr.replace(/\./g, ':');
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>