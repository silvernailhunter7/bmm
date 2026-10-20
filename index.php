<?php
require_once 'koneksi.php';
require_once 'includes/api_gempa.php';

$page_title = "BMKG Gorontalo - Portal Resmi";

// Fetch Master ADM4 Gorontalo untuk Filter Cuaca
$master_adm4 = get_master_adm4_gorontalo();$data_cuaca  = get_bmkg_cuaca_gorontalo();

// 2. Susun Struktur Hierarchy untuk Dropdown (Kabupaten -> Kecamatan -> ADM4)
$dropdown_data = [];
foreach ($master_adm4 as$item) {
    $kab  =$item['kab'];
    $kec  =$item['kec'];
    $adm4 =$item['adm4'];
    
    if (!isset($dropdown_data[$kab])) {
        $dropdown_data[$kab] = [];
    }
    $dropdown_data[$kab][$kec] =$adm4;
}

// Fetch Berita Utama
$list_berita = [];
try {
    $stmt =$pdo->query("SELECT * FROM berita ORDER BY id DESC");
    $list_berita =$stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {$list_berita = [];
}

// Fetch Autogempa Terkini
$gempa_terkini = get_bmkg_gempa('autogempa');$g_mag       = !empty($gempa_terkini['Magnitude']) ?$gempa_terkini['Magnitude'] : '5.2';
$g_tgl       = !empty($gempa_terkini['Tanggal']) ? $gempa_terkini['Tanggal'] : date('d M Y');$g_jam       = !empty($gempa_terkini['Jam']) ?$gempa_terkini['Jam'] : '12:44:23 WITA';
$g_kedalaman = !empty($gempa_terkini['Kedalaman']) ? $gempa_terkini['Kedalaman'] : '10 km';$g_wilayah   = !empty($gempa_terkini['Wilayah']) ?$gempa_terkini['Wilayah'] : 'Gorontalo dan sekitarnya';
$g_potensi   = !empty($gempa_terkini['Potensi']) ? $gempa_terkini['Potensi'] : 'Tidak berpotensi Tsunami';$g_shakemap  = !empty($gempa_terkini['Shakemap']) ?$gempa_terkini['Shakemap'] : '';

// Masukkan URL Video YouTube resmi di sini
$youtube_url = "https://www.youtube.com/embed/aCwY-XoBTsg?si=QDxDq7NSxVIj12Lq";

// Fungsi Pembersih URL YouTube Anti-Error 153
function get_youtube_embed_url_clean($url) {
    $video_id = '';
    
    // Murni mengekstrak 11 karakter ID Video saja (misal: aCwY-XoBTsg)
    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
        $video_id = $matches[1];
    }
    
    if ($video_id) {
        // Gabungkan murni dengan parameter embed standar tanpa ?si=
        return "https://www.youtube.com/embed/" . $video_id . "?rel=0&enablejsapi=1";
    }
    
    return "https://www.youtube.com/embed/aCwY-XoBTsg?rel=0";
}

$embed_src = get_youtube_embed_url_clean($youtube_url);

// ATUR TAUTAN NAVIGASI LAYANAN DI SINI (DAPAT DIUBAH SESUAI KEBUTUHAN)
$layanan_links = [
    'cuaca'         => 'pages/cuaca_harian.php',
    'bandara'       => 'pages/cuaca_penerbangan.php',
    'maritim'       => 'pages/cuaca_pelabuhan.php',
    'iklim'         => 'pages/iklim_10.php',
    'kualitas_udara'=> 'pages/kualitas_udara.php',
    'siaran_pers'   => 'pages/siaran_pers.php',
    'pelayanan_data'=> 'pages/pelayanan.php',
    'transparansi'  => 'pages/transparansi.php',
    'pengaduan'     => 'pages/pengaduan.php',
];

include 'includes/header.php';
?>

<style>
    /* STYLE BANNER PERINGATAN DINI */
    .banner-peringatan {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #a7f3d0;
        background-color: #f0fdf4;
    }
    .badge-peringatan {
        background-color: #059669;
        color: #ffffff;
        font-weight: 700;
        padding: 12px 20px;
        font-size: 0.82rem;
        letter-spacing: 0.5px;
    }

    /* QUICK MENU OVAL INTERAKTIF */
 <style>
    .layanan-wrapper {
        max-width: 1000px;
        margin: 0 auto;
    }
    .layanan-item {
        text-decoration: none !important;
        color: #1e293b;
        transition: transform 0.2s ease, color 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .layanan-item:hover {
        transform: translateY(-5px);
        color: #0284c7;
    }
    .icon-circle {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        background-color: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        color: #1e293b;
        margin-bottom: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        transition: background-color 0.2s ease, color 0.2s ease;
    }
    .layanan-item:hover .icon-circle {
        background-color: #e0f2fe;
        color: #0284c7;
    }
    .layanan-title {
        font-size: 0.85rem;
        font-weight: 700;
        text-align: center;
        line-height: 1.3;
        color: #334155;
    }
</style>

<div class="container-fluid px-lg-5 my-3">

<!-- BANNER PERINGATAN DINI DINAMIS GORONTALO -->
<div id="wrapper-peringatan-dini" class="banner-peringatan d-flex flex-column flex-md-row align-items-md-center mb-4 shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid #a7f3d0; background-color: #f0fdf4;">
    <div id="badge-status-peringatan" class="badge-peringatan d-flex align-items-center gap-2 text-nowrap" style="background-color: #059669; color: #ffffff; font-weight: 700; padding: 12px 20px; font-size: 0.82rem; letter-spacing: 0.5px;">
        <i class="bi bi-shield-check fs-5"></i> PERINGATAN DINI
    </div>
    <div id="text-narasi-peringatan" class="px-3 py-2 text-success fw-medium small mb-0 flex-grow-1">
        Memeriksa data Peringatan Dini BMKG Gorontalo
    </div>
    <div class="px-3 py-1 text-end">
        <a href="https://www.bmkg.go.id/cuaca/peringatan-dini-cuaca" target="_blank" class="text-decoration-none small text-muted" style="font-size: 0.72rem;">
        </a>
    </div>
</div>

    <!-- HERO BANNER PTSP DENGAN EMBED VIDEO YOUTUBE -->
<div class="card border-0 rounded-4 p-4 p-md-5 mb-5 text-white shadow-sm" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
    <div class="row align-items-center g-4">
        
        <!-- TEKS DAN TOMBOL PTSP -->
        <div class="col-lg-7">
            <span class="badge bg-white text-primary rounded-pill px-3 py-1.5 mb-3 fw-semibold shadow-sm" style="font-size: 0.8rem;">
                <i class="bi bi-shield-check me-1"></i> Pelayanan Terpadu Satu Pintu (PTSP)
            </span>
            <h2 class="fw-bold mb-3 display-6" style="line-height: 1.2;">
                Akses Informasi Meteorologi & Geofisika Gorontalo
            </h2>
            <p class="mb-4 text-white-50" style="font-size: 0.96rem; max-width: 600px; line-height: 1.6;">
                Layanan resmi informasi prakiraan cuaca, analisis iklim dasarian, mitigasi gempabumi, dan permohonan data BMKG terintegrasi.
            </p>
            <div class="d-flex flex-wrap gap-2.5">
                <a href="#container-hasil-cuaca" class="btn btn-warning rounded-pill px-4 py-2.5 fw-bold text-dark shadow-sm">
                    <i class="bi bi-search me-1.5"></i> Cek Cuaca Kecamatan
                </a>
                <a href="pages/pelayanan_data.php" class="btn btn-light rounded-pill px-4 py-2.5 fw-bold text-primary shadow-sm">
                    <i class="bi bi-file-earmark-text me-1.5"></i> Permintaan Data Resmi
                </a>
            </div>
        </div>

        <!-- EMBED VIDEO YOUTUBE (LENGKAP DENGAN REFERRERPOLICY UNTUK MENCEGAH ERROR 153) -->
        <div class="col-lg-5">
            <div class="ratio ratio-16x9 rounded-4 overflow-hidden shadow-lg border border-2 border-white border-opacity-25">
                <iframe 
                    src="<?= htmlspecialchars($embed_src) ?>" 
                    title="Video Profil BMKG" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                    referrerpolicy="strict-origin-when-cross-origin"
                    allowfullscreen>
                </iframe>
            </div>
        </div>

    </div>
</div>

    <!-- 3. QUICK MENU INTERAKTIF -->
    <div class="row g-3 mb-5">
        <div class="col-6 col-md-4">
            <a href="pages/cuaca_harian.php" class="card quick-card-interactive h-100 rounded-4 shadow-sm text-decoration-none p-3 text-center">
                <div class="oval-bg bg-primary-subtle mb-2">
                    <i class="bi bi-clock-history fs-3 text-primary oval-icon"></i>
                </div>
                <span class="fw-bold text-dark d-block mb-0" style="font-size: 0.9rem;">Prakicu Per Jam</span>
                <small class="text-muted" style="font-size: 0.75rem;">Detail per-Kecamatan</small>
            </a>
        </div>
        <div class="col-6 col-md-4">
            <a href="pages/gempa_terkini.php" class="card quick-card-interactive h-100 rounded-4 shadow-sm text-decoration-none p-3 text-center">
                <div class="oval-bg bg-danger-subtle mb-2">
                    <i class="bi bi-activity fs-3 text-danger oval-icon"></i>
                </div>
                <span class="fw-bold text-dark d-block mb-0" style="font-size: 0.9rem;">Peta Gempa Realtime</span>
                <small class="text-muted" style="font-size: 0.75rem;">Shakemap & Parameter</small>
            </a>
        </div>
        <div class="col-6 col-md-4">
            <a href="pages/iklim_10.php" class="card quick-card-interactive h-100 rounded-4 shadow-sm text-decoration-none p-3 text-center">
                <div class="oval-bg bg-success-subtle mb-2">
                    <i class="bi bi-calendar3 fs-3 text-success oval-icon"></i>
                </div>
                <span class="fw-bold text-dark d-block mb-0" style="font-size: 0.9rem;">Analisis Dasarian</span>
                <small class="text-muted" style="font-size: 0.75rem;">Info Iklim 10 Harian</small>
            </a>
        </div>
    </div>

<!-- SECTION PRAKIRAAN CUACA KECAMATAN -->
<div class="card card-modern p-4 border rounded-4 shadow-sm bg-white mb-5" id="container-hasil-cuaca">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-cloud-sun text-primary me-2"></i>Prakiraan Cuaca
            </h4>
            <p class="text-muted small mb-0">Pilih wilayah administrasi untuk detail prakiraan cuaca per jam di Provinsi Gorontalo</p>
        </div>

        <!-- DROPDOWN FILTER WILAYAH (BERDERET HORIZONTAL) -->
        <div class="d-flex flex-row align-items-center gap-2 flex-wrap flex-lg-nowrap">
            <select id="select-kabupaten" class="form-select form-select-sm rounded-pill px-3" style="min-width: 180px;">
                <option value="">-- Pilih Kab/Kota --</option>
                <?php foreach ($dropdown_data as $kab_nama =>$kec_list): ?>
                    <option value="<?= htmlspecialchars($kab_nama) ?>"><?= htmlspecialchars($kab_nama) ?></option>
                <?php endforeach; ?>
            </select>

            <select id="select-kecamatan" class="form-select form-select-sm rounded-pill px-3" style="min-width: 180px;" disabled>
                <option value="">-- Pilih Kecamatan --</option>
            </select>

            <select id="select-desa" class="form-select form-select-sm rounded-pill px-3" style="min-width: 180px;" disabled>
                <option value="">-- Semua Desa --</option>
            </select>
        </div>
    </div>

    <!-- AREA TAMPILAN HASIL PRAKIRAAN CUACA -->
    <div id="weather-content-area"></div>
</div>

    <!-- AREA TAMPILAN HASIL PRAKIRAAN CUACA -->
    <div id="weather-content-area">
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-2" role="status"></div>
            <p class="text-muted small mb-0">Memuat prakiraan cuaca wilayah Gorontalo...</p>
        </div>
    </div>
</div>

    <!-- 5. ROW SEJAJAR: BERITA & GEMPA TERKINI -->
    <div class="row g-4 align-items-stretch mb-4">
        
        <!-- KOLOM KIRI: BERITA UTAMA BMKG (SCROLLABLE) -->
        <div class="col-lg-7 col-xl-8 d-flex">
            <div class="card card-modern border shadow-sm w-100 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark fs-6">
                            <i class="bi bi-newspaper text-primary me-2"></i>Berita & Informasi Resmi BMKG
                        </h5>
                        <a href="pages/berita.php" class="btn btn-primary btn-sm rounded-pill px-3">
                            Lihat Semua Berita <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>

                    <div class="card-body p-3 overflow-y-auto" style="max-height: 520px;">
                        <?php if (!empty($list_berita)): ?>
                            <div class="d-flex flex-column gap-3">
                                <?php foreach ($list_berita as$b): ?>
                                    <?php 
                                    $img_src = !empty($b['gambar']) ?$b['gambar'] : 'assets/img/default-news.jpg';
                                    if (!preg_match("~^(?:f|ht)tps?://~i", $img_src) && !file_exists($img_src)) {
                                        $img_src = 'uploads/' . basename($img_src);
                                    }
                                    $judul    =$b['judul'] ?? 'Berita BMKG';
                                    $kategori =$b['kategori'] ?? 'Meteorologi';
                                    $isi      =$b['isi'] ?? $b['konten'] ?? $b['deskripsi'] ?? '';
                                    $penulis  =$b['penulis'] ?? $b['author'] ?? 'Admin BMKG';$tanggal  = $b['created_at'] ?? $b['tgl_terbit'] ?? date('Y-m-d');
                                    ?>
                                    <div class="card border-0 bg-light rounded-3 overflow-hidden shadow-sm">
                                        <div class="row g-0 align-items-center">
                                            <div class="col-md-4 col-4">
                                                <img src="<?= htmlspecialchars($img_src) ?>" 
                                                     class="img-fluid w-100 object-fit-cover" 
                                                     style="height: 110px;" 
                                                     alt="Gambar Berita"
                                                     onerror="this.src='https://www.bmkg.go.id/asset/img/logo/logo-bmkg.png';">
                                            </div>
                                            <div class="col-md-8 col-8 p-2.5 px-3">
                                                <span class="badge bg-primary rounded-pill mb-1" style="font-size: 0.68rem;"><?= htmlspecialchars($kategori) ?></span>
                                                <h6 class="fw-bold text-dark mb-1 fs-6 text-truncate"><?= htmlspecialchars($judul) ?></h6>
                                                <p class="text-muted small mb-2 text-truncate-2" style="font-size: 0.78rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                    <?= htmlspecialchars(substr(strip_tags($isi), 0, 90)) ?>...
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.72rem;">
                                                    <span><i class="bi bi-calendar3 me-1"></i><?= date('d M Y', strtotime($tanggal)) ?></span>
                                                    <span><i class="bi bi-person me-1"></i><?= htmlspecialchars($penulis) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <p class="text-muted mb-0">Belum ada berita terbaru yang dipublikasikan.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: SHAKEMAP & GEMPA TERKINI -->
        <div class="col-lg-5 col-xl-4 d-flex">
            <div class="card card-modern border shadow-sm w-100 h-100 d-flex flex-column overflow-hidden">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center py-3 px-4">
                    <h5 class="mb-0 fw-bold fs-6">
                        <i class="bi bi-activity me-2"></i>Gempabumi Terkini
                    </h5>
                </div>
                
                <div class="card-body p-3 text-center d-flex flex-column justify-content-between">
                    <div>
                        <div class="p-2 border rounded-3 bg-light shadow-sm d-inline-block mb-2" style="max-width: 100%;">
                            <img src="includes/proxy_shakemap.php?img=<?= urlencode($g_shakemap) ?>" 
                                 referrerpolicy="no-referrer"
                                 class="img-fluid rounded-2" 
                                 alt="Peta Shakemap BMKG"
                                 style="max-height: 220px; max-width: 1800px; object-fit: contain;">
                        </div>

                        <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                            <div class="display-5 fw-extrabold text-danger"><?= htmlspecialchars($g_mag) ?></div>
                            <div class="text-start">
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-0.5 fw-bold" style="font-size: 0.68rem;">Magnitudo</span>
                                <div class="text-muted small fw-semibold" style="font-size: 0.72rem;"><?= htmlspecialchars($g_tgl) ?> - <?= htmlspecialchars($g_jam) ?></div>
                            </div>
                        </div>

                        <div class="table-responsive text-start px-2">
                            <table class="table table-borderless table-sm align-middle mb-0" style="font-size: 0.82rem;">
                                <tr>
                                    <td class="text-muted text-nowrap" style="width: 100px;"><i class="bi bi-arrow-down-up text-primary me-1"></i>Kedalaman</td>
                                    <td class="fw-bold text-dark">: <?= htmlspecialchars($g_kedalaman) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted text-nowrap"><i class="bi bi-geo-alt text-danger me-1"></i>Lokasi</td>
                                    <td class="fw-bold text-dark">: <?= htmlspecialchars($g_wilayah) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted text-nowrap"><i class="bi bi-shield-exclamation text-warning me-1"></i>Potensi</td>
                                    <td class="fw-bold text-success">: <?= htmlspecialchars($g_potensi) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mt-2 pt-2 border-top">
                        <a href="pages/gempa_terkini.php" class="btn btn-danger btn-sm rounded-pill px-4 py-1.5 fw-semibold">
                            <i class="bi bi-map me-1"></i> Peta Interaktif Gempa
                        </a>
                    </div>
                </div>
            </div>
        </div>
<div class="container my-5 py-3">
    <div class="layanan-wrapper">
        <!-- JUDUL HEADER SECTION -->
        <div class="text-center mb-4">
            <h3 class="fw-bold text-dark mb-1">Layanan Lainnya</h3>
            <p class="text-muted small mb-0">Akses terintegrasi untuk seluruh layanan meteorologi, klimatologi, dan publik</p>
        </div>

        <!-- GRID LAYANAN (TEKS BERADA DI BAWAH VEKTOR) -->
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-3 g-4 justify-content-center text-center">
            
            <!-- 1. Cuaca Kab & Kec -->
            <div class="col">
                <a href="<?= htmlspecialchars($layanan_links['cuaca']) ?>" class="layanan-item">
                    <div class="icon-circle">
                        <i class="bi bi-cloud-drizzle"></i>
                    </div>
                    <span class="layanan-title">Cuaca Kab & Kec</span>
                </a>
            </div>

            <!-- 2. Cuaca Bandara -->
            <div class="col">
                <a href="<?= htmlspecialchars($layanan_links['bandara']) ?>" class="layanan-item">
                    <div class="icon-circle">
                        <i class="bi bi-thermometer-sun"></i>
                    </div>
                    <span class="layanan-title">Cuaca Bandara</span>
                </a>
            </div>

            <!-- 3. Cuaca Maritim -->
            <div class="col">
                <a href="<?= htmlspecialchars($layanan_links['maritim']) ?>" class="layanan-item">
                    <div class="icon-circle">
                        <i class="bi bi-water"></i>
                    </div>
                    <span class="layanan-title">Cuaca Maritim</span>
                </a>
            </div>

            <!-- 4. Informasi Iklim -->
            <div class="col">
                <a href="<?= htmlspecialchars($layanan_links['iklim']) ?>" class="layanan-item">
                    <div class="icon-circle">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <span class="layanan-title">Informasi Iklim</span>
                </a>
            </div>

            <!-- 5. Kualitas Udara -->
            <div class="col">
                <a href="<?= htmlspecialchars($layanan_links['kualitas_udara']) ?>" class="layanan-item">
                    <div class="icon-circle">
                        <i class="bi bi-droplet"></i>
                    </div>
                    <span class="layanan-title">Kualitas Udara</span>
                </a>
            </div>

            <!-- 6. Siaran Pers -->
            <div class="col">
                <a href="<?= htmlspecialchars($layanan_links['siaran_pers']) ?>" class="layanan-item">
                    <div class="icon-circle">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <span class="layanan-title">Siaran Pers</span>
                </a>
            </div>

            <!-- 7. Pelayanan Data -->
            <div class="col">
                <a href="<?= htmlspecialchars($layanan_links['pelayanan']) ?>" class="layanan-item">
                    <div class="icon-circle">
                        <i class="bi bi-database"></i>
                    </div>
                    <span class="layanan-title">Pelayanan Data</span>
                </a>
            </div>

            <!-- 8. Transparansi Kinerja -->
            <div class="col">
                <a href="<?= htmlspecialchars($layanan_links['transparansi']) ?>" class="layanan-item">
                    <div class="icon-circle">
                        <i class="bi bi-people"></i>
                    </div>
                    <span class="layanan-title">Transparansi Kinerja</span>
                </a>
            </div>

            <!-- 9. Pengaduan & WBS -->
            <div class="col">
                <a href="<?= htmlspecialchars($layanan_links['pengaduan']) ?>" class="layanan-item">
                    <div class="icon-circle">
                        <i class="bi bi-shield-exclamation"></i>
                    </div>
                    <span class="layanan-title">Pengaduan</span>
                </a>
            </div>

        </div>
    </div>
</div>

<!-- JAVASCRIPT DINAMIS DROPDOWN KABUPATEN/KECAMATAN -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const dropdownData = <?= json_encode($dropdown_data) ?>;
    const allCuacaData = <?= json_encode($data_cuaca) ?>;

    const selectKab   = document.getElementById("select-kabupaten");
    const selectKec   = document.getElementById("select-kecamatan");
    const selectDesa  = document.getElementById("select-desa");
    const contentArea = document.getElementById("weather-content-area");

    const defaultKecamatan = ["Kota Barat", "Limboto", "Tilamuta", "Marisa", "Kwandang", "Suwawa"];

    // FUNGSI RENDER VEKTOR IKON SESUAI DENGAN DESKRIPSI KONDISI & SUHU
    function renderWeatherVector(cInfo) {
        if (cInfo && cInfo.icon && cInfo.icon.trim() !== '') {
            return `<i class="bi ${cInfo.icon} display-6"></i>`;
        }
        
        // Mapping Vektor Otomatis Berdasarkan Teks Kondisi
        const k = (cInfo && cInfo.kondisi) ? cInfo.kondisi.toLowerCase() : '';
        
        if (k.includes('petir')) {
            return `<i class="bi bi-cloud-lightning-rain-fill text-danger display-6"></i>`;
        } else if (k.includes('hujan sedang') || k.includes('hujan lebat')) {
            return `<i class="bi bi-cloud-rain-heavy-fill text-primary display-6"></i>`;
        } else if (k.includes('hujan')) {
            return `<i class="bi bi-cloud-drizzle-fill text-primary display-6"></i>`;
        } else if (k.includes('berawan')) {
            return `<i class="bi bi-cloud-sun-fill text-warning display-6"></i>`;
        } else if (k.includes('cerah')) {
            return `<i class="bi bi-sun-fill text-warning display-6"></i>`;
        }
        
        return `<i class="bi bi-cloud-sun-fill text-warning display-6"></i>`;
    }

    // 1. EVENT KABUPATEN CHANGED
    selectKab.addEventListener("change", function () {
        const kabSelected = this.value;
        selectKec.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        selectDesa.innerHTML = '<option value="">-- Semua Desa --</option>';
        selectDesa.disabled = true;

        if (kabSelected && dropdownData[kabSelected]) {
            selectKec.disabled = false;
            for (let kecNama in dropdownData[kabSelected]) {
                let opt = document.createElement("option");
                opt.value = dropdownData[kabSelected][kecNama];
                opt.dataset.nama = kecNama;
                opt.textContent = kecNama;
                selectKec.appendChild(opt);
            }
            renderKabupatenView(kabSelected);
        } else {
            selectKec.disabled = true;
            loadDefaultWeather();
        }
    });

    // 2. EVENT KECAMATAN CHANGED
    selectKec.addEventListener("change", function () {
        const adm4Code = this.value;
        const selectedOpt = this.options[this.selectedIndex];
        const kecNama = selectedOpt ? selectedOpt.dataset.nama : '';

        selectDesa.innerHTML = '<option value="">-- Semua Desa --</option>';

        if (adm4Code) {
            selectDesa.disabled = false;
            let opt = document.createElement("option");
            opt.value = adm4Code;
            opt.textContent = "Kel./Desa " + kecNama;
            selectDesa.appendChild(opt);

            renderDetailCuacaJam(adm4Code, kecNama);
        } else {
            selectDesa.disabled = true;
            if (selectKab.value) renderKabupatenView(selectKab.value);
        }
    });

    // 3. EVENT DESA CHANGED
    selectDesa.addEventListener("change", function () {
        const adm4Code = this.value;
        if (adm4Code) {
            const label = this.options[this.selectedIndex].text;
            renderDetailCuacaJam(adm4Code, label);
        }
    });

    function loadDefaultWeather() {
        const filtered = allCuacaData.filter(item => defaultKecamatan.includes(item.kecamatan));
        renderCardGrid(filtered, "Prakiraan Cuaca Wilayah Utama Gorontalo Hari Ini:");
    }

    function renderKabupatenView(kabNama) {
        const filtered = allCuacaData.filter(item => item.kabupaten === kabNama);
        renderCardGrid(filtered, "Prakiraan Cuaca Kecamatan di " + kabNama + ":");
    }

    // RENDER CARD GRID DENGAN VEKTOR ICON PRESISI
    function renderCardGrid(listData, titleLabel) {
        if (!listData || listData.length === 0) {
            contentArea.innerHTML = `<div class="alert alert-warning rounded-4">Data cuaca tidak ditemukan.</div>`;
            return;
        }

        let cardsHtml = listData.map(item => {
            const firstJam = Object.keys(item.cuaca)[0];
            const cInfo = item.cuaca[firstJam] || {};
            const vectorIcon = renderWeatherVector(cInfo);

            return `
                <div class="col-md-6 col-lg-4">
                    <div class="card border rounded-4 shadow-sm p-3 bg-light h-100">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold text-dark mb-0">${item.kecamatan}</h6>
                            <small class="text-muted" style="font-size:0.72rem;">${item.kabupaten}</small>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    ${vectorIcon}
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0 text-dark">${cInfo.suhu || '28'}&deg;C</h4>
                                    <small class="text-secondary fw-semibold" style="font-size: 0.8rem;">${cInfo.kondisi || 'Cerah Berawan'}</small>
                                </div>
                            </div>
                            <button onclick="triggerSelectKec('${item.adm4}')" class="btn btn-outline-primary btn-sm rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                Detail <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        contentArea.innerHTML = `
            <div class="mb-3">
                <small class="fw-bold text-muted uppercase tracking-wider">
                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>${titleLabel}
                </small>
            </div>
            <div class="row g-3">${cardsHtml}</div>
        `;
    }

    // RENDER DETAIL PER JAM
    function renderDetailCuacaJam(adm4Code, locationLabel) {
        const targetData = allCuacaData.find(item => item.adm4 === adm4Code);

        if (targetData && targetData.cuaca) {
            let itemsHtml = Object.entries(targetData.cuaca).map(([jam, c]) => {
                const vectorIcon = renderWeatherVector(c);
                return `
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="border rounded-4 p-3 text-center bg-light h-100">
                            <small class="text-muted d-block mb-1">${jam}</small>
                            <div class="my-2 d-flex align-items-center justify-content-center" style="height: 48px;">
                                ${vectorIcon}
                            </div>
                            <h5 class="fw-bold mb-0 text-dark">${c.suhu}&deg;C</h5>
                            <small class="text-secondary d-block mt-1" style="font-size:0.75rem; line-height: 1.2;">${c.kondisi}</small>
                        </div>
                    </div>
                `;
            }).join('');

            contentArea.innerHTML = `
                <div class="alert alert-info rounded-4 d-flex align-items-center gap-2 mb-3 py-2 px-3">
                    <i class="bi bi-geo-alt-fill fs-5"></i>
                    <span class="fw-semibold">Detail Prakiraan Per Jam untuk: <strong>${locationLabel}</strong></span>
                </div>
                <div class="row g-2.5">${itemsHtml}</div>
            `;
        } else {
            contentArea.innerHTML = `<div class="alert alert-warning rounded-4">Detail cuaca belum tersedia.</div>`;
        }
    }

    window.triggerSelectKec = function(adm4Code) {
        const item = allCuacaData.find(i => i.adm4 === adm4Code);
        if (item) {
            selectKab.value = item.kabupaten;
            selectKab.dispatchEvent(new Event('change'));
            setTimeout(() => {
                selectKec.value = adm4Code;
                selectKec.dispatchEvent(new Event('change'));
            }, 50);
        }
    };

    loadDefaultWeather();
});
    
    document.addEventListener("DOMContentLoaded", function() {
        fetch('includes/proxy_warning.php')
            .then(response => response.json())
            .then(data => {
                const wrapper = document.getElementById('wrapper-peringatan-dini');
                const badge = document.getElementById('badge-status-peringatan');
                const text = document.getElementById('text-narasi-peringatan');

                if (data.active) {
                    // TAMPILAN JIKA ADA PERINGATAN DINI AKTIF (KUNING / MERAH)
                    wrapper.style.backgroundColor = '#fef2f2';
                    wrapper.style.borderColor = '#fca5a5';
                    badge.style.backgroundColor = '#dc2626';
                    badge.innerHTML = '<i class="bi bi-exclamation-triangle-fill fs-5"></i> PERINGATAN DINI CUACA';
                    text.className = 'px-3 py-2 text-danger fw-bold small mb-0 flex-grow-1';
                    text.innerText = data.message;
                } else {
                    // TAMPILAN JIKA KONDISI AMAN / KONDUSIF (HIJAU)
                    wrapper.style.backgroundColor = '#f0fdf4';
                    wrapper.style.borderColor = '#a7f3d0';
                    badge.style.backgroundColor = '#059669';
                    badge.innerHTML = '<i class="bi bi-shield-check fs-5"></i> PERINGATAN DINI';
                    text.className = 'px-3 py-2 text-success fw-medium small mb-0 flex-grow-1';
                    text.innerText = data.message;
                }
            })
            .catch(err => {
                document.getElementById('text-narasi-peringatan').innerText = "Tidak ada peringatan dini cuaca di wilayah Provinsi Gorontalo saat ini. Kondisi terpantau kondusif.";
            });
    });
</script>

<?php include 'includes/footer.php'; ?>