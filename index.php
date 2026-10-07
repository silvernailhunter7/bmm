<?php
require_once 'koneksi.php';

try {
    // Ambil Berita Admin
    $stmt_berita = $pdo->query("SELECT * FROM berita ORDER BY created_at DESC LIMIT 3");
    $berita_list = $stmt_berita->fetchAll();

    // Ambil Gempa Terkini
    $stmt_gempa = $pdo->query("SELECT * FROM gempa ORDER BY tanggal_jam DESC LIMIT 1");
    $gempa = $stmt_gempa->fetch();

    // Ambil Cuaca Kecamatan Jam 12:00
    $stmt_cuaca = $pdo->query("SELECT * FROM cuaca_perjam WHERE jam='12:00' ORDER BY kabupaten, kecamatan ASC");
    $cuaca_list = $stmt_cuaca->fetchAll();
} catch (PDOException $e) {
    die("Gagal mengambil data dari database: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>BMKG Gorontalo - Portal Resmi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .bmkg-header { background-color: #0d47a1; color: #fff; }
        .navbar-bmkg { background-color: #1565c0; }
        .navbar-bmkg .nav-link { color: #ffffff !important; font-weight: 500; }
        #map-pinpoint { height: 210px; width: 100%; border-radius: 6px; }
        .card-gempa { background: linear-gradient(135deg, #d32f2f, #b71c1c); color: white; }
        @media (min-width: 992px) {
            .navbar-bmkg .nav-item.dropdown:hover .dropdown-menu { display: block; margin-top: 0; }
        }
        .nav-link.disabled-parent { cursor: default; }
    </style>
</head>
<body class="bg-light">

<!-- Header Logo BMKG -->
<div class="bmkg-header py-2 px-4 d-flex align-items-center">
    <img src="https://www.bmkg.go.id/asset/img/logo/logo-bmkg.png" alt="Logo BMKG" height="50" class="me-3">
    <div>
        <h5 class="mb-0 fw-bold">BADAN METEOROLOGI, KLIMATOLOGI, DAN GEOFISIKA</h5>
        <small class="text-warning">Stasiun Meteorologi & Geofisika Provinsi Gorontalo</small>
    </div>
</div>

<!-- Navigation Bar dengan Hover Menu -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-bmkg sticky-top">
    <div class="container-fluid px-4">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link active" href="index.php">Beranda</a></li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle disabled-parent" href="javascript:void(0)">Meteorologi</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="pages/cuaca_harian.php">Cuaca Harian Per Jam</a></li>
                        <li><a class="dropdown-item" href="pages/cuaca_pelabuhan.php">Cuaca Pelabuhan Gorontalo</a></li>
                        <li><a class="dropdown-item" href="pages/satelit.php">Citra Satelit</a></li>
                        <li><a class="dropdown-item" href="pages/radar_cuaca.php">Radar Cuaca Realtime</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle disabled-parent" href="javascript:void(0)">Klimatologi</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="pages/iklim_semester.php">Iklim Per Semester</a></li>
                        <li><a class="dropdown-item" href="pages/cuaca_10harian.php">Cuaca Per 10 Harian</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle disabled-parent" href="javascript:void(0)">Geofisika</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="pages/gempa_terkini.php">Gempa Terkini</a></li>
                        <li><a class="dropdown-item" href="pages/gempa_5mb.php">Gempa > 5.0 M</a></li>
                    </ul>
                </li>
            </ul>
            <a href="login.php" class="btn btn-outline-light btn-sm">Login Admin</a>
        </div>
    </div>
</nav>

<!-- Main Container Layout (Wireframe Match) -->
<div class="container-fluid my-4 px-4">
    <div class="row g-3">
        
        <!-- SISI KIRI: BERITA YANG DIINPUT OLEH ADMIN -->
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white fw-bold">
                    BERITA & INFORMASI RESMI BMKG GORONTALO
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php if (!empty($berita_list)): ?>
                            <?php foreach ($berita_list as $b): ?>
                                <div class="col-12">
                                    <div class="card h-100 border-0 shadow-sm bg-light">
                                        <div class="row g-0 align-items-center">
                                            <div class="col-md-4">
                                                <img src="<?= htmlspecialchars($b['gambar'], ENT_QUOTES, 'UTF-8') ?>" class="img-fluid rounded-start w-100" style="height:150px; object-fit:cover;" alt="Berita">
                                            </div>
                                            <div class="col-md-8">
                                                <div class="card-body">
                                                    <span class="badge bg-primary mb-1"><?= htmlspecialchars($b['kategori'], ENT_QUOTES, 'UTF-8') ?></span>
                                                    <h5 class="card-title fw-bold text-dark"><?= htmlspecialchars($b['judul'], ENT_QUOTES, 'UTF-8') ?></h5>
                                                    <p class="card-text text-muted small mb-2"><?= htmlspecialchars(substr($b['isi'], 0, 120), ENT_QUOTES, 'UTF-8') ?>...</p>
                                                    <small class="text-secondary"><?= date('d M Y', strtotime($b['created_at'])) ?> | Oleh: <?= htmlspecialchars($b['penulis'], ENT_QUOTES, 'UTF-8') ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Belum ada berita yang diterbitkan.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- SISI KANAN (PINPOINT & INFO GEMPA) -->
        <div class="col-lg-4 d-flex flex-column gap-3">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white fw-bold py-2">
                    PETA PINPOINT GEMPA TERKINI
                </div>
                <div class="card-body p-2">
                    <div id="map-pinpoint"></div>
                </div>
            </div>

            <div class="card card-gempa shadow-sm flex-grow-1">
                <div class="card-header bg-transparent border-bottom-0 fw-bold fs-6 py-2">
                    INFORMASI GEMPA TERKINI (GORONTALO)
                </div>
                <div class="card-body pt-0">
                    <?php if ($gempa): ?>
                        <div class="display-6 fw-bold mb-1"><?= htmlspecialchars($gempa['magnitudo'], ENT_QUOTES, 'UTF-8') ?> <small class="fs-6">SR / M</small></div>
                        <p class="mb-1 small"><strong>Waktu:</strong> <?= date('d-m-Y H:i:s', strtotime($gempa['tanggal_jam'])) ?> WITA</p>
                        <p class="mb-1 small"><strong>Kedalaman:</strong> <?= htmlspecialchars($gempa['kedalaman'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="mb-1 small"><strong>Lokasi:</strong> <?= htmlspecialchars($gempa['lokasi'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="mb-1 small"><strong>Potensi:</strong> <span class="badge bg-warning text-dark"><?= htmlspecialchars($gempa['potensi'], ENT_QUOTES, 'UTF-8') ?></span></p>
                        <p class="mb-0 small"><strong>Dirasakan:</strong> <?= htmlspecialchars($gempa['dirasakan'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php else: ?>
                        <p class="small">Data gempa belum tersedia.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <!-- INFORMASI CUACA KECAMATAN GORONTALO -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white fw-bold d-flex justify-content-between align-items-center">
                    <span>INFORMASI CUACA KECAMATAN GORONTALO (REALTIME)</span>
                    <a href="pages/cuaca_harian.php" class="btn btn-sm btn-light fw-bold text-primary">Lihat Cuaca Per Jam &raquo;</a>
                </div>
                <div class="card-body">
                    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-6 g-3">
                        <?php if (!empty($cuaca_list)): ?>
                            <?php foreach ($cuaca_list as $c): ?>
                                <div class="col">
                                    <div class="card h-100 border-info text-center shadow-sm py-2">
                                        <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($c['kecamatan'], ENT_QUOTES, 'UTF-8') ?></h6>
                                        <small class="text-muted fs-7"><?= htmlspecialchars($c['kabupaten'], ENT_QUOTES, 'UTF-8') ?></small>
                                        <div class="fs-4 text-primary fw-bold my-1"><?= (int)$c['suhu'] ?>°C</div>
                                        <span class="badge bg-info text-dark mb-1 align-self-center"><?= htmlspecialchars($c['kondisi'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <small class="text-muted fs-8">Angin: <?= (int)$c['kecepatan_angin'] ?> km/j</small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    <?php if ($gempa): ?>
        var gLat = <?= (float)$gempa['latitude'] ?>;
        var gLng = <?= (float)$gempa['longitude'] ?>;
    <?php else: ?>
        var gLat = 0.5401; var gLng = 123.0600;
    <?php endif; ?>

    var map = L.map('map-pinpoint').setView([gLat, gLng], 8);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; BMKG'
    }).addTo(map);

    <?php if ($gempa): ?>
    L.marker([gLat, gLng]).addTo(map)
        .bindPopup("<b>Lokasi Gempa</b><br>M: <?= htmlspecialchars($gempa['magnitudo'], ENT_QUOTES, 'UTF-8') ?><br><?= htmlspecialchars($gempa['lokasi'], ENT_QUOTES, 'UTF-8') ?>")
        .openPopup();
    <?php endif; ?>
</script>
</body>
</html>
