<?php
require_once 'koneksi.php';
require_once 'includes/api_gempa.php';

$page_title = "BMKG Gorontalo - Portal Resmi";

// Fetch Master ADM4 Gorontalo untuk Filter Cuaca
$master_adm4 = get_master_adm4_gorontalo();

// Grouping Kabupaten/Kota & Kecamatan
$kabupaten_list = [];
foreach ($master_adm4 as$item) {
    $kab =$item['kab'];
    $kec =$item['kec'];
    $code =$item['adm4'];
    if (!isset($kabupaten_list[$kab])) {
        $kabupaten_list[$kab] = [];
    }
    $kabupaten_list[$kab][] = ['kec' => $kec, 'adm4' =>$code];
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
    .quick-card-interactive {
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        border: 1px solid #e2e8f0 !important;
        background: #ffffff;
    }
    .quick-card-interactive .oval-bg {
        width: 90%;
        height: 48px;
        border-radius: 50px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.35s ease;
    }
    .quick-card-interactive .oval-icon {
        transition: transform 0.3s ease;
    }
    .quick-card-interactive:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 20px -5px rgba(2, 132, 199, 0.15) !important;
        border-color: #0284c7 !important;
    }
    .quick-card-interactive:hover .oval-bg {
        width: 100%;
        height: 54px;
    }
    .quick-card-interactive:hover .oval-icon {
        transform: scale(1.25) rotate(5deg);
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

    <!-- 2. HERO BANNER UTAMA -->
    <div class="card border-0 rounded-4 overflow-hidden mb-4 shadow-sm" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff;">
        <div class="card-body p-4 p-lg-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge bg-white text-primary fw-bold px-3 py-1.5 rounded-pill mb-3 shadow-sm" style="font-size: 0.78rem;">
                        <i class="bi bi-shield-check text-primary me-1"></i> Pelayanan Terpadu Satu Pintu (PTSP)
                    </span>
                    <h2 class="fw-bold text-white mb-2 fs-3 fs-lg-2">
                        Akses Informasi Meteorologi & Geofisika Gorontalo
                    </h2>
                    <p class="text-white-50 mb-4" style="font-size: 0.95rem; max-width: 680px;">
                        Layanan resmi informasi prakiraan cuaca, analisis iklim dasarian, mitigasi gempabumi, dan permohonan data BMKG terintegrasi.
                    </p>
                    <div class="d-flex flex-wrap gap-2.5">
                        <a href="pages/cuaca_harian.php" class="btn btn-warning rounded-pill px-4 py-2 fw-bold text-dark shadow-sm">
                            <i class="bi bi-search me-1.5"></i> Cek Cuaca Kecamatan
                        </a>
                        <a href="pages/ptsp.php" class="btn btn-light rounded-pill px-4 py-2 fw-semibold text-primary shadow-sm">
                            <i class="bi bi-file-earmark-text-fill me-1.5"></i> Permintaan Data Resmi
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-center d-none d-lg-block">
                    <img src="https://www.bmkg.go.id/asset/img/logo/logo-bmkg.png" 
                         class="img-fluid drop-shadow opacity-75" 
                         style="max-height: 140px; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.2));" 
                         alt="Logo BMKG Portal">
                </div>
            </div>
        </div>
    </div>

    <!-- 3. QUICK MENU INTERAKTIF -->
    <div class="row g-3 mb-5">
        <div class="col-6 col-md-3">
            <a href="pages/cuaca_harian.php" class="card quick-card-interactive h-100 rounded-4 shadow-sm text-decoration-none p-3 text-center">
                <div class="oval-bg bg-primary-subtle mb-2">
                    <i class="bi bi-clock-history fs-3 text-primary oval-icon"></i>
                </div>
                <span class="fw-bold text-dark d-block mb-0" style="font-size: 0.9rem;">Prakicu Per Jam</span>
                <small class="text-muted" style="font-size: 0.75rem;">Detail per-Kecamatan</small>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="pages/citra_satelit.php" class="card quick-card-interactive h-100 rounded-4 shadow-sm text-decoration-none p-3 text-center">
                <div class="oval-bg bg-info-subtle mb-2">
                    <i class="bi bi-globe-americas fs-3 text-info oval-icon"></i>
                </div>
                <span class="fw-bold text-dark d-block mb-0" style="font-size: 0.9rem;">Satelit Himawari-9</span>
                <small class="text-muted" style="font-size: 0.75rem;">Animasi Liputan Awan</small>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="pages/gempa_terkini.php" class="card quick-card-interactive h-100 rounded-4 shadow-sm text-decoration-none p-3 text-center">
                <div class="oval-bg bg-danger-subtle mb-2">
                    <i class="bi bi-activity fs-3 text-danger oval-icon"></i>
                </div>
                <span class="fw-bold text-dark d-block mb-0" style="font-size: 0.9rem;">Peta Gempa Realtime</span>
                <small class="text-muted" style="font-size: 0.75rem;">Shakemap & Parameter</small>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="pages/iklim_dasarian.php" class="card quick-card-interactive h-100 rounded-4 shadow-sm text-decoration-none p-3 text-center">
                <div class="oval-bg bg-success-subtle mb-2">
                    <i class="bi bi-calendar3 fs-3 text-success oval-icon"></i>
                </div>
                <span class="fw-bold text-dark d-block mb-0" style="font-size: 0.9rem;">Analisis Dasarian</span>
                <small class="text-muted" style="font-size: 0.75rem;">Info Iklim 10 Harian</small>
            </a>
        </div>
    </div>

<!-- SECTION PRAKIRAAN CUACA INTERAKTIF KECAMATAN -->
<div class="mb-5">
    
    <!-- BARIS ATAS: JUDUL & DROPDOWN FILTER HORIZONTAL -->
    <div class="row align-items-center g-3 mb-4">
        <div class="col-lg-5">
            <h3 class="fw-bold text-dark mb-1">Prakiraan Cuaca</h3>
            <p class="text-muted small mb-0">Pilih wilayah administrasi untuk detail prakiraan per jam</p>
        </div>
        
        <div class="col-lg-7">
            <!-- DROPDOWN FILTER HORIZONTAL -->
            <div class="row g-2 align-items-center">
                <div class="col-12 col-sm-4">
                    <select id="select-kabupaten" class="form-select rounded-pill shadow-sm w-100" style="font-size: 0.88rem;">
                        <option value="">-- Pilih Kab/Kota --</option>
                        <?php foreach ($kabupaten_list as $kab => $kec_items): ?>
                            <option value="<?= htmlspecialchars($kab) ?>"><?= htmlspecialchars($kab) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-sm-4">
                    <select id="select-kecamatan" class="form-select rounded-pill shadow-sm w-100" style="font-size: 0.88rem;" disabled>
                        <option value="">-- Pilih Kecamatan --</option>
                    </select>
                </div>

                <div class="col-12 col-sm-4">
                    <select id="select-desa" class="form-select rounded-pill shadow-sm w-100" style="font-size: 0.88rem;" disabled>
                        <option value="">-- Semua Desa --</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- WADAH DISPLAY HASIL CUACA (TEPAT DI BAWAH / FULL WIDTH) -->
    <div id="container-hasil-cuaca" class="border rounded-4 p-4 text-center bg-white shadow-sm w-100" style="border-style: dashed !important;">
        <p class="text-muted mb-0" style="font-size: 0.92rem;">
            Tidak ada data cuaca untuk lokasi atau tanggal ini. Silakan pilih wilayah pada dropdown di atas.
        </p>
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
                                 style="max-height: 220px; width: auto; object-fit: contain;">
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

    </div>
</div>

<!-- JAVASCRIPT DINAMIS DROPDOWN KABUPATEN/KECAMATAN -->
<script>
    const dataKabupaten = <?= json_encode($kabupaten_list) ?>;

    const selectKab = document.getElementById('select-kabupaten');
    const selectKec = document.getElementById('select-kecamatan');
    const selectDesa = document.getElementById('select-desa');
    const containerCuaca = document.getElementById('container-hasil-cuaca');

    selectKab.addEventListener('change', function() {
        const valKab = this.value;
        selectKec.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        selectDesa.innerHTML = '<option value="">-- Semua Desa --</option>';
        selectDesa.disabled = true;

        if (valKab && dataKabupaten[valKab]) {
            selectKec.disabled = false;
            dataKabupaten[valKab].forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.adm4;
                opt.textContent = item.kec;
                selectKec.appendChild(opt);
            });
        } else {
            selectKec.disabled = true;
            containerCuaca.innerHTML = '<p class="text-muted mb-0" style="font-size: 0.92rem;">Tidak ada data cuaca untuk lokasi atau tanggal ini.</p>';
        }
    });

    selectKec.addEventListener('change', function() {
        const adm4Code = this.value;
        const nameKec = this.options[this.selectedIndex].text;

        if (adm4Code) {
            selectDesa.disabled = false;
            containerCuaca.innerHTML = `
                <div class="d-flex justify-content-center align-items-center py-4">
                    <div class="spinner-border text-primary me-2" role="status"></div>
                    <span class="text-muted fw-semibold">Mengambil Prakiraan Cuaca BMKG untuk ${nameKec}...</span>
                </div>
            `;

            // Simulasi Render Data Prakiraan Cuaca
            setTimeout(() => {
                containerCuaca.innerHTML = `
                    <div class="text-start">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <h6 class="fw-bold text-dark mb-0"><i class="bi bi-geo-alt-fill text-primary me-1"></i> Prakiraan Cuaca ${nameKec}</h6>
                            <a href="pages/cuaca_harian.php?adm4=${adm4Code}" class="btn btn-primary btn-sm rounded-pill px-3">Detail Selengkapnya</a>
                        </div>
                        <div class="row g-2 text-center">
                            <div class="col-4 col-md-2">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">08.00 WITA</small>
                                    <i class="bi bi-sun fs-3 text-warning my-1 d-block"></i>
                                    <span class="fw-bold small d-block">Cerah</span>
                                    <small class="text-primary fw-semibold">28°C</small>
                                </div>
                            </div>
                            <div class="col-4 col-md-2">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">11.00 WITA</small>
                                    <i class="bi bi-cloud-sun fs-3 text-warning my-1 d-block"></i>
                                    <span class="fw-bold small d-block">Cerah Berawan</span>
                                    <small class="text-primary fw-semibold">31°C</small>
                                </div>
                            </div>
                            <div class="col-4 col-md-2">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">14.00 WITA</small>
                                    <i class="bi bi-cloud-rain fs-3 text-info my-1 d-block"></i>
                                    <span class="fw-bold small d-block">Hujan Ringan</span>
                                    <small class="text-primary fw-semibold">29°C</small>
                                </div>
                            </div>
                            <div class="col-4 col-md-2">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">17.00 WITA</small>
                                    <i class="bi bi-cloud fs-3 text-secondary my-1 d-block"></i>
                                    <span class="fw-bold small d-block">Berawan</span>
                                    <small class="text-primary fw-semibold">27°C</small>
                                </div>
                            </div>
                            <div class="col-4 col-md-2">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">20.00 WITA</small>
                                    <i class="bi bi-moon-stars fs-3 text-indigo my-1 d-block"></i>
                                    <span class="fw-bold small d-block">Cerah Berawan</span>
                                    <small class="text-primary fw-semibold">25°C</small>
                                </div>
                            </div>
                            <div class="col-4 col-md-2">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">23.00 WITA</small>
                                    <i class="bi bi-cloud-moon fs-3 text-indigo my-1 d-block"></i>
                                    <span class="fw-bold small d-block">Berawan</span>
                                    <small class="text-primary fw-semibold">24°C</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }, 500);
        } else {
            selectDesa.disabled = true;
            containerCuaca.innerHTML = '<p class="text-muted mb-0" style="font-size: 0.92rem;">Tidak ada data cuaca untuk lokasi atau tanggal ini.</p>';
        }
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