<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/includes/api_gempa.php';
require_once __DIR__ . '/includes/clear.php';

$page_title = "BMKG Gorontalo - Portal Resmi";

// Fetch Master ADM4 Gorontalo untuk Filter Cuaca
$master_adm4 = get_master_adm4_gorontalo();

// Susun Struktur Hierarchy untuk Dropdown (Kabupaten -> Kecamatan -> ADM4)
$dropdown_data = [];
foreach ($master_adm4 as $item) {
    $kab  = $item['kab'];
    $kec  = $item['kec'];
    $adm4 = $item['adm4'];
    
    if (!isset($dropdown_data[$kab])) {
        $dropdown_data[$kab] = [];
    }
    $dropdown_data[$kab][$kec] = $adm4;
}

// Ambil Data Cuaca Realtime yang Sama Persis via Helper
$data_cuaca = get_cuaca_gorontalo_processed();

// AMBIL DATA HASIL PARSING DARI PAGE CUACA HARIAN
$data_cuaca_harian = [];
foreach ($master_adm4 as $item) {
    // Memanggil fungsi cache yang sama persis dengan yang dipakai di halaman cuaca harian
    $raw_api = get_cuaca_adm4_smart_cache($item['adm4']); 
    
    $map_jam = [];
    $cuaca_raw = $raw_api['data'][0]['cuaca'] ?? $raw_api['cuaca'] ?? [];

    if (is_array($cuaca_raw)) {
        foreach ($cuaca_raw as $group) {
            $sub_items = isset($group[0]) ? $group : [$group];
            foreach ($sub_items as $c_item) {
                if (!is_array($c_item)) continue;
                
                $ldt = $c_item['local_datetime'] ?? $c_item['datetime'] ?? '';
                if (empty($ldt)) continue;

                $clean_dt = str_replace('T', ' ', $ldt);
                $parts = explode(' ', $clean_dt);
                if (isset($parts[1])) {
                    $hour = (int)substr($parts[1], 0, 2);
                    $jam_key = sprintf("%02d:00", $hour);
                    
                    if (!isset($map_jam[$jam_key])) {
                        $map_jam[$jam_key] = [
                            'suhu' => $c_item['t'] ?? '28',
                            'kondisi' => $c_item['weather_desc'] ?? $c_item['weather_desc_en'] ?? 'Berawan'
                        ];
                    }
                }
            }
        }
    }

    $data_cuaca_harian[] = [
        'adm4' => $item['adm4'],
        'kecamatan' => $item['kec'],
        'kabupaten' => $item['kab'],
        'cuaca' => $map_jam
    ];
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

// URL Video YouTube Resmi
$youtube_url = "https://www.youtube.com/embed/aCwY-XoBTsg?si=QDxDq7NSxVIj12Lq";

function get_youtube_embed_url_clean($url) {$video_id = '';
    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url,$matches)) {
        $video_id =$matches[1];
    }
    if ($video_id) {
        return "https://www.youtube.com/embed/" . $video_id . "?rel=0&enablejsapi=1";
    }
    return "https://www.youtube.com/embed/aCwY-XoBTsg?rel=0";
}

$embed_src = get_youtube_embed_url_clean($youtube_url);

$layanan_links = [
    'cuaca'         => 'pages/cuaca_harian.php',
    'bandara'       => 'pages/cuaca_penerbangan.php',
    'maritim'       => 'pages/cuaca_pelabuhan.php',
    'iklim'         => 'pages/iklim_10.php',
    'kualitas_udara' => 'pages/kualitas_udara.php',
    'siaran_pers'   => 'pages/siaran_pers.php',
    'pelayanan_data' => 'pages/ptsp.php',
    'transparansi'  => 'pages/transparansi.php',
    'pengaduan'     => 'pages/pengaduan.php',
];

include 'includes/header.php';
?>

<style>
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
<div id="wrapper-peringatan-dini" class="banner-peringatan d-flex flex-column flex-md-row align-items-md-center mb-4 shadow-sm">
    <div id="badge-status-peringatan" class="badge-peringatan d-flex align-items-center gap-2 text-nowrap">
        <i class="bi bi-shield-check fs-5"></i> PERINGATAN DINI
    </div>
    <div id="text-narasi-peringatan" class="px-3 py-2 text-success fw-medium small mb-0 flex-grow-1">
        Memeriksa data Peringatan Dini BMKG Gorontalo...
    </div>
</div>

<!-- HERO BANNER PTSP DENGAN EMBED VIDEO YOUTUBE -->
<div class="card border-0 rounded-4 p-4 p-md-5 mb-5 text-white shadow-sm" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
    <div class="row align-items-center g-4">
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
                <a href="pages/ptsp.php" class="btn btn-light rounded-pill px-4 py-2.5 fw-bold text-primary shadow-sm">
                    <i class="bi bi-file-earmark-text me-1.5"></i> Permintaan Data Resmi
                </a>
            </div>
        </div>

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

<!-- QUICK MENU INTERAKTIF -->
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

        <!-- DROPDOWN FILTER WILAYAH -->
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
    <div id="weather-content-area">
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-2" role="status"></div>
            <p class="text-muted small mb-0">Memuat prakiraan cuaca wilayah Gorontalo...</p>
        </div>
    </div>
</div>

<!-- ROW SEJAJAR: BERITA & GEMPA TERKINI -->
<div class="row g-4 align-items-stretch mb-4">
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
                            <div class="text-muted small fw-semibold" style="font-size: 0.72rem;"><?= htmlspecialchars($g_tgl) ?> <?= htmlspecialchars($g_jam) ?></div>
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
</div>

<div class="container my-5 py-3">
    <div class="layanan-wrapper">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-dark mb-1">Layanan Lainnya</h3>
            <p class="text-muted small mb-0">Akses terintegrasi untuk seluruh layanan meteorologi, klimatologi, dan publik</p>
        </div>

        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-3 g-4 justify-content-center text-center">
            <div class="col">
                <a href="<?= htmlspecialchars($layanan_links['cuaca']) ?>" class="layanan-item">
                    <div class="icon-circle"><i class="bi bi-cloud-drizzle"></i></div>
                    <span class="layanan-title">Cuaca Kab & Kec</span>
                </a>
            </div>
            <div class="col">
                <a href="<?= htmlspecialchars($layanan_links['bandara']) ?>" class="layanan-item">
                    <div class="icon-circle"><i class="bi bi-thermometer-sun"></i></div>
                    <span class="layanan-title">Cuaca Bandara</span>
                </a>
            </div>
            <div class="col">
                <a href="<?= htmlspecialchars($layanan_links['maritim']) ?>" class="layanan-item">
                    <div class="icon-circle"><i class="bi bi-water"></i></div>
                    <span class="layanan-title">Cuaca Maritim</span>
                </a>
            </div>
            <div class="col">
                <a href="<?= htmlspecialchars($layanan_links['iklim']) ?>" class="layanan-item">
                    <div class="icon-circle"><i class="bi bi-calendar-event"></i></div>
                    <span class="layanan-title">Informasi Iklim</span>
                </a>
            </div>
            <div class="col">
                <a href="<?= htmlspecialchars($layanan_links['kualitas_udara']) ?>" class="layanan-item">
                    <div class="icon-circle"><i class="bi bi-droplet"></i></div>
                    <span class="layanan-title">Kualitas Udara</span>
                </a>
            </div>
            <div class="col">
                <a href="<?= htmlspecialchars($layanan_links['siaran_pers']) ?>" class="layanan-item">
                    <div class="icon-circle"><i class="bi bi-file-earmark-text"></i></div>
                    <span class="layanan-title">Siaran Pers</span>
                </a>
            </div>
            <div class="col">
                <a href="<?= htmlspecialchars($layanan_links['pelayanan_data']) ?>" class="layanan-item">
                    <div class="icon-circle"><i class="bi bi-database"></i></div>
                    <span class="layanan-title">Pelayanan Data</span>
                </a>
            </div>
            <div class="col">
                <a href="<?= htmlspecialchars($layanan_links['transparansi']) ?>" class="layanan-item">
                    <div class="icon-circle"><i class="bi bi-people"></i></div>
                    <span class="layanan-title">Transparansi Kinerja</span>
                </a>
            </div>
            <div class="col">
                <a href="<?= htmlspecialchars($layanan_links['pengaduan']) ?>" class="layanan-item">
                    <div class="icon-circle"><i class="bi bi-shield-exclamation"></i></div>
                    <span class="layanan-title">Pengaduan</span>
                </a>
            </div>
        </div>
    </div>
</div>

</div>

<!-- JAVASCRIPT DINAMIS CUACA & PERINGATAN DINI -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const dropdownData = <?= json_encode($dropdown_data) ?>;
    // LANGSUNG MEMBACA DATA HASIL OLOHAN PAGE CUACA HARIAN
    const allCuacaData = <?= json_encode($data_cuaca_harian) ?>; 

    const selectKab   = document.getElementById("select-kabupaten");
    const selectKec   = document.getElementById("select-kecamatan");
    const selectDesa  = document.getElementById("select-desa");
    const contentArea = document.getElementById("weather-content-area");

    // DAFTAR KECAMATAN DEFAULT DENGAN MAPPING ADM4 LENGKAP
    const defaultKecamatan = [
        { adm4: "75.01.02.2020", nama: "Telaga" },
        { adm4: "75.01.01.1008", nama: "Limboto" },
        { adm4: "75.01.03.2012", nama: "Batudaa" },
        { adm4: "75.01.04.2004", nama: "Tibawa" },
        { adm4: "75.01.05.2007", nama: "Batudaa Pantai" },
        { adm4: "75.01.10.2004", nama: "Telaga Biru" },
        { adm4: "75.01.11.2001", nama: "Bongomeme" },
        { adm4: "75.01.16.2002", nama: "Pulubala" },
        { adm4: "75.01.18.2008", nama: "Tilango" },
        { adm4: "75.01.21.2005", nama: "Asparaga" },
        { adm4: "75.02.01.2014", nama: "Paguyaman" },
        { adm4: "75.02.02.2007", nama: "Wonosari" },
        { adm4: "75.02.04.2011", nama: "Tilamuta" },
        { adm4: "75.02.07.2003", nama: "Paguyaman Pantai" },
        { adm4: "75.03.01.2007", nama: "Tapa" },
        { adm4: "75.03.03.2012", nama: "Suwawa" },
        { adm4: "75.03.04.2004", nama: "Bone Pantai" },
        { adm4: "75.03.10.2006", nama: "Bone Raya" },
        { adm4: "75.03.18.2001", nama: "Pinogu" },
        { adm4: "75.04.01.2004", nama: "Popayato" },
        { adm4: "75.04.04.2008", nama: "Marisa" },
        { adm4: "75.05.01.2004", nama: "Atinggola" },
        { adm4: "75.05.02.2001", nama: "Kwandang" },
        { adm4: "75.05.04.2007", nama: "Sumalata" }
    ];

    function renderWeatherVector(desc) {
        const k = (desc || '').toLowerCase();
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

    // Mendapatkan slot jam aktif WITA (misal 18:00 WITA)
    function getJamSekarangWITA() {
        const now = new Date();
        const options = { timeZone: 'Asia/Makassar', hour: '2-digit', hour12: false };
        const hourNum = parseInt(new Intl.DateTimeFormat('id-ID', options).format(now));
        const slotHours = [0, 3, 6, 9, 12, 15, 18, 21];
        const closest = slotHours.reduce((prev, curr) => 
            Math.abs(curr - hourNum) < Math.abs(prev - hourNum) ? curr : prev
        );
        return String(closest).padStart(2, '0') + ":00";
    }

    function renderCardGrid(listData, titleLabel) {
        if (!listData || listData.length === 0) {
            contentArea.innerHTML = `<div class="alert alert-warning rounded-4 text-center">Data cuaca tidak ditemukan.</div>`;
            return;
        }

        const jamAktif = getJamSekarangWITA();

        let cardsHtml = listData.map(item => {
            let cInfo = {};
            if (item.cuaca) {
                // Ambil data jam aktif yang sama dengan di tabel cuaca harian
                cInfo = item.cuaca[jamAktif] || Object.values(item.cuaca)[0] || {};
            }

            const vectorIcon = renderWeatherVector(cInfo.kondisi);

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
                                    <small class="text-secondary fw-semibold" style="font-size: 0.8rem;">${cInfo.kondisi || 'Cerah'}</small>
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
                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>${titleLabel} (${jamAktif} WITA)
                </small>
            </div>
            <div class="row g-3">${cardsHtml}</div>
        `;
    }

    function loadDefaultWeatherCards() {
        const defaultNames = defaultKecamatan.map(d => d.nama);
        const defaultCodes = defaultKecamatan.map(d => d.adm4);

        const filtered = allCuacaData.filter(item => 
            defaultNames.includes(item.kecamatan) || defaultCodes.includes(item.adm4)
        );

        renderCardGrid(filtered.length > 0 ? filtered : allCuacaData.slice(0, 6), "Prakiraan Cuaca Wilayah Utama Gorontalo Hari Ini:");
    }

    loadDefaultWeatherCards();
});

// PROXY PERINGATAN DINI CUACA
document.addEventListener("DOMContentLoaded", function() {
    fetch('includes/proxy_warning.php')
        .then(response => response.json())
        .then(data => {
            const wrapper = document.getElementById('wrapper-peringatan-dini');
            const badge = document.getElementById('badge-status-peringatan');
            const text = document.getElementById('text-narasi-peringatan');

            if (data.active) {
                wrapper.style.backgroundColor = '#fef2f2';
                wrapper.style.borderColor = '#fca5a5';
                badge.style.backgroundColor = '#dc2626';
                badge.innerHTML = '<i class="bi bi-exclamation-triangle-fill fs-5"></i> PERINGATAN DINI CUACA';
                text.className = 'px-3 py-2 text-danger fw-bold small mb-0 flex-grow-1';
                text.innerText = data.message;
            } else {
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