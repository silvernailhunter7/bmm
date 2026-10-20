<?php 
require_once '../koneksi.php'; 
require_once '../includes/api_gempa.php';

$page_title = "Peta Gempabumi Terkini - BMKG Gorontalo";
$is_subpage = true;

// Fetch Data Gempa Terkini
$gempa_terkini = get_bmkg_gempa('autogempa');

$g_mag       = !empty($gempa_terkini['Magnitude']) ?$gempa_terkini['Magnitude'] : '4.2';
$g_tgl       = !empty($gempa_terkini['Tanggal']) ? $gempa_terkini['Tanggal'] : date('d M Y');$g_jam       = !empty($gempa_terkini['Jam']) ?$gempa_terkini['Jam'] : '20:46:11 WIB';
$g_kedalaman = !empty($gempa_terkini['Kedalaman']) ? $gempa_terkini['Kedalaman'] : '5 km';$g_wilayah   = !empty($gempa_terkini['Wilayah']) ?$gempa_terkini['Wilayah'] : 'Pusat gempa berada di darat 26 km Timur Laut Kab. Gayo Lues';
$g_potensi   = !empty($gempa_terkini['Potensi']) ? $gempa_terkini['Potensi'] : 'Tidak berpotensi Tsunami';$g_shakemap  = !empty($gempa_terkini['Shakemap']) ?$gempa_terkini['Shakemap'] : '';

// Parsing Koordinat Lat & Long
$coords_str  = !empty($gempa_terkini['Coordinates']) ?$gempa_terkini['Coordinates'] : '4.16,97.51';
$coords_arr  = explode(',',$coords_str);
$lat = isset($coords_arr[0]) ? trim($coords_arr[0]) : '4.16';$lng = isset($coords_arr[1]) ? trim($coords_arr[1]) : '97.51';

include '../includes/header.php';
?>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    #map-gempa {
        height: 580px;
        width: 100%;
        border-radius: 16px;
        z-index: 1;
    }
    .leaflet-tile {
        max-width: none !important;
        max-height: none !important;
    }
    .card-popup-gempa {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 1000;
        width: 320px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
</style>

<div class="container-fluid px-lg-5 my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item">Geofisika</li>
            <li class="breadcrumb-item active">Gempa Terkini</li>
        </ol>
    </nav>

    <div class="card card-modern p-3 border shadow-sm rounded-4 position-relative overflow-hidden bg-white">
        
        <!-- CONTAINER PETA MAP LEAFLET -->
        <div id="map-gempa"></div>

        <!-- POPUP OVERLAY DETAIL GEMPA (GAYA BMKG JUANDA) -->
        <div class="card-popup-gempa p-3 border">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-warning text-dark fw-bold rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                    Gempa Dirasakan
                </span>
                <button type="button" onclick="document.querySelector('.card-popup-gempa').style.display='none';" class="btn-close btn-sm"></button>
            </div>

            <div class="text-muted small mb-2" style="font-size: 0.78rem;">
                <i class="bi bi-clock me-1"></i><?= htmlspecialchars($g_tgl) ?> • <?= htmlspecialchars($g_jam) ?>
            </div>

            <h6 class="fw-bold text-dark mb-3" style="font-size: 0.88rem; line-height: 1.4;">
                <?= htmlspecialchars($g_wilayah) ?>
            </h6>

            <div class="table-responsive">
                <table class="table table-sm table-borderless align-middle mb-3" style="font-size: 0.82rem;">
                    <tr>
                        <td class="text-muted"><i class="bi bi-activity text-danger me-1"></i>Magnitudo</td>
                        <td class="fw-bold text-end text-dark"><?= htmlspecialchars($g_mag) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted"><i class="bi bi-arrow-down-up text-primary me-1"></i>Kedalaman</td>
                        <td class="fw-bold text-end text-dark"><?= htmlspecialchars($g_kedalaman) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted"><i class="bi bi-geo-alt text-warning me-1"></i>Lokasi</td>
                        <td class="fw-bold text-end text-dark"><?= htmlspecialchars($coords_str) ?></td>
                    </tr>
                </table>
            </div>

            <?php if (!empty($g_shakemap)): ?>
                <a href="../includes/proxy_shakemap.php?img=<?= urlencode($g_shakemap) ?>" target="_blank" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold btn-sm shadow-sm">
                    Lihat Shakemap BMKG
                </a>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const lat = <?= json_encode($lat) ?>;
    const lng = <?= json_encode($lng) ?>;

    // 1. Inisialisasi Peta Leaflet
    const map = L.map('map-gempa', {
        zoomControl: true,
        scrollWheelZoom: true
    }).setView([lat, lng], 6);

    // 2. Gunakan Tile Server CartoDB Positron / OSM yang Lebih Cepat dan Bebas Blokir
// Opsi Peta Esri World Street (Gratis, Tanpa API Key, Tampilan Bersih)
L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
    maxZoom: 18,
    attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DeLorme, NAVTEQ, USGS, Intermap, iPC, NRCAN, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom'
}).addTo(map);

    // 3. Icon Marker Pulsing Merah BMKG
    const gempaIcon = L.divIcon({
        className: 'custom-gempa-marker',
        html: `
            <div style="position: relative; width: 24px; height: 24px;">
                <div style="position: absolute; width: 100%; height: 100%; background: rgba(220, 38, 38, 0.4); border-radius: 50%; animation: pulse 1.5s infinite;"></div>
                <div style="position: absolute; top: 4px; left: 4px; width: 16px; height: 16px; background: #dc2626; border: 2px solid white; border-radius: 50%;"></div>
            </div>
            <style>
                @keyframes pulse {
                    0% { transform: scale(1); opacity: 1; }
                    100% { transform: scale(3); opacity: 0; }
                }
            </style>
        `,
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });

    // 4. Tambah Marker Gempa & Radius
    L.marker([lat, lng], { icon: gempaIcon }).addTo(map);
    L.circle([lat, lng], {
        color: '#dc2626',
        fillColor: '#f87171',
        fillOpacity: 0.25,
        radius: 30000
    }).addTo(map);

    // 5. SOLUSI PETA RUSAK: Trigger invalidateSize() setelah DOM Selesai Dimuat
    setTimeout(function() {
        map.invalidateSize();
    }, 300);
});
</script>

<?php include '../includes/footer.php'; ?>