<?php
require_once 'koneksi.php';

$q_gempa = mysqli_query($koneksi, "SELECT * FROM gempa ORDER BY tanggal_jam DESC LIMIT 1");
$gempa = mysqli_fetch_assoc($q_gempa);

$q_cuaca = mysqli_query($koneksi, "SELECT * FROM cuaca_kecamatan ORDER BY kabupaten, kecamatan ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMKG Gorontalo - Badan Meteorologi, Klimatologi, dan Geofisika</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .bmkg-header { background-color: #0d47a1; color: #fff; }
        .navbar-bmkg { background-color: #1565c0; }
        .navbar-bmkg .nav-link { color: #ffffff !important; font-weight: 500; }
        .card-gempa { background: linear-gradient(135deg, #d32f2f, #b71c1c); color: white; }
        #map-radar { height: 350px; width: 100%; border-radius: 8px; }
    </style>
</head>
<body>

<div class="bmkg-header py-2 px-4 d-flex align-items-center">
    <img src="https://www.bmkg.go.id/asset/img/logo/logo-bmkg.png" alt="Logo BMKG" height="50" class="me-3">
    <div>
        <h5 class="mb-0 fw-bold">BADAN METEOROLOGI, KLIMATOLOGI, DAN GEOFISIKA</h5>
        <small class="text-warning">Stasiun Meteorologi & Geofisika Provinsi Gorontalo</small>
    </div>
</div>

<nav class="navbar navbar-expand-lg navbar-dark navbar-bmkg sticky-top">
    <div class="container-fluid px-4">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link active" href="index.php">Beranda</a></li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Meteorologi</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="pages/cuaca_harian.php">Cuaca Harian</a></li>
                        <li><a class="dropdown-item" href="pages/cuaca_bandara.php">Cuaca Bandara Jalaluddin</a></li>
                        <li><a class="dropdown-item" href="pages/cuaca_pelabuhan.php">Cuaca Pelabuhan Gorontalo</a></li>
                        <li><a class="dropdown-item" href="pages/satelit.php">Citra Satelit</a></li>
                        <li><a class="dropdown-item" href="pages/radar_cuaca.php">Radar Cuaca Realtime</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Klimatologi</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="pages/iklim_semester.php">Iklim Per Semester</a></li>
                        <li><a class="dropdown-item" href="pages/cuaca_10harian.php">Cuaca Per 10 Harian</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Geofisika</a>
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

<div class="container-fluid my-4 px-4">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card card-gempa shadow h-100">
                <div class="card-header bg-transparent border-bottom-0 fw-bold fs-5">GEMPA BUMI TERKINI (GORONTALO)</div>
                <div class="card-body">
                    <?php if ($gempa): ?>
                        <div class="display-6 fw-bold mb-2"><?= htmlspecialchars($gempa['magnitudo'], ENT_QUOTES, 'UTF-8') ?> <small class="fs-6">SR / M</small></div>
                        <p class="mb-1"><strong>Waktu:</strong> <?= date('d-m-Y H:i:s', strtotime($gempa['tanggal_jam'])) ?> WITA</p>
                        <p class="mb-1"><strong>Kedalaman:</strong> <?= htmlspecialchars($gempa['kedalaman'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="mb-1"><strong>Lokasi:</strong> <?= htmlspecialchars($gempa['lokasi'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="mb-1"><strong>Potensi:</strong> <span class="badge bg-warning text-dark"><?= htmlspecialchars($gempa['potensi'], ENT_QUOTES, 'UTF-8') ?></span></p>
                        <p class="mb-0 mt-2"><strong>Dirasakan:</strong> <?= htmlspecialchars($gempa['dirasakan'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php else: ?>
                        <p>Data gempa belum tersedia.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow h-100">
                <div class="card-header bg-dark text-white fw-bold">RADAR CUACA REALTIME GORONTALO</div>
                <div class="card-body p-2">
                    <div id="map-radar"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white fw-bold">CUACA KECAMATAN DI GORONTALO (REALTIME)</div>
                <div class="card-body">
                    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-3">
                        <?php while ($c = mysqli_fetch_assoc($q_cuaca)): ?>
                            <div class="col">
                                <div class="card h-100 border-info">
                                    <div class="card-body text-center">
                                        <h6 class="card-title fw-bold mb-0"><?= htmlspecialchars($c['kecamatan'], ENT_QUOTES, 'UTF-8') ?></h6>
                                        <small class="text-muted"><?= htmlspecialchars($c['kabupaten'], ENT_QUOTES, 'UTF-8') ?></small>
                                        <div class="my-2 fs-3 text-primary fw-bold"><?= (int)$c['suhu'] ?>°C</div>
                                        <span class="badge bg-info text-dark mb-2"><?= htmlspecialchars($c['kondisi'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <div class="small text-muted">Kelembapan: <?= (int)$c['kelembapan'] ?>% | Angin: <?= (int)$c['kecepatan_angin'] ?> km/j</div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map = L.map('map-radar').setView([0.5401, 123.0600], 9);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; BMKG Gorontalo' }).addTo(map);

    <?php if ($gempa): ?>
    L.marker([<?= (float)$gempa['latitude'] ?>, <?= (float)$gempa['longitude'] ?>])
        .addTo(map)
        .bindPopup("<b>Gempa Terkini</b><br>M: <?= htmlspecialchars($gempa['magnitudo'], ENT_QUOTES, 'UTF-8') ?><br><?= htmlspecialchars($gempa['lokasi'], ENT_QUOTES, 'UTF-8') ?>")
        .openPopup();
    <?php endif; ?>
</script>
</body>
</html>