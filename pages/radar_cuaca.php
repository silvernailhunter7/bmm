<?php 
require_once '../koneksi.php'; 

$page_title = "Citra Radar Cuaca Realtime - BMKG Gorontalo";
$is_subpage = true;

include '../includes/header.php';
?>

<div class="container-fluid px-lg-5 my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item">Meteorologi</li>
            <li class="breadcrumb-item active">Radar Cuaca Realtime</li>
        </ol>
    </nav>

    <div class="card card-modern p-3 border shadow-sm">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3 gap-2 px-1">
            <div>
                <h4 class="fw-bold mb-1 text-slate-800">
                    <i class="bi bi-broadcast text-primary me-2"></i>Citra Radar Cuaca Gorontalo
                </h4>
                <p class="text-muted small mb-0">Deteksi dini intensitas hujan dan pergerakan awan Cumulonimbus (CB).</p>
            </div>
            <button onclick="location.reload();" class="btn btn-primary rounded-pill px-3 btn-sm align-self-start align-self-md-auto">
                <i class="bi bi-arrow-clockwise me-1"></i> Update Radar BMKG
            </button>
        </div>

        <!-- GRID LAYOUT: GAMBAR RADAR (KOLOM LEBAR) + KETERANGAN (SAMPING) -->
        <div class="row g-3 align-items-stretch">
            
            <!-- KOLOM KIRI: GAMBAR GIF RADAR (PORSI 9/12, MARGIN PADAT/MEPET) -->
            <div class="col-lg-9 col-xl-9 d-flex">
                <div class="text-center overflow-hidden p-0 rounded-3 bg-dark border w-100 d-flex align-items-center justify-content-center">
                    <img src="../includes/proxy_radar.php?t=<?= time() ?>" 
                         referrerpolicy="no-referrer"
                         class="img-fluid rounded-3" 
                         alt="Citra Radar Cuaca Gorontalo" 
                         style="max-height: 800px; width: 70%; object-fit: cover;">
                </div>
            </div>

            <!-- KOLOM KANAN: LEGEND INTENSITAS & BUBBLE DESKRIPSI RADAR -->
                <div class="col-lg-4 col-xl-3 d-flex flex-column justify-content-between">
                    <div class="bg-white p-3 rounded-3 shadow-sm border h-100 d-flex flex-column justify-content-between">
                        
                        <div>
                            <!-- KETERANGAN INTENSITAS dBZ -->
                            <h6 class="fw-bold text-dark mb-3" style="font-size: 0.9rem;">Keterangan Intensitas Hujan (dBZ):</h6>
                            
                            <div class="d-flex flex-column gap-2 mb-4">
                                <div class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-light">
                                    <span class="badge bg-info text-dark">Light Blue</span>
                                    <span class="small text-muted fw-semibold">15 - 25 dBZ <br><small>(Hujan Ringan)</small></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-light">
                                    <span class="badge bg-primary">Blue/Green</span>
                                    <span class="small text-muted fw-semibold">25 - 35 dBZ <br><small>(Hujan Sedang)</small></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-light">
                                    <span class="badge bg-warning text-dark">Yellow/Orange</span>
                                    <span class="small text-muted fw-semibold">35 - 45 dBZ <br><small>(Hujan Lebat)</small></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-light">
                                    <span class="badge bg-danger">Red/Purple</span>
                                    <span class="small text-muted fw-semibold">> 45 dBZ <br><small>(Sangat Lebat/Es)</small></span>
                                </div>
                            </div>

                            <!-- BUBBLE DESKRIPSI RADAR GORONTALO -->
                            <div class="p-3 rounded-4 border-0 shadow-sm" style="background-color: #f0f9ff; border-left: 4px solid #0284c7 !important;">
                                <div class="d-flex align-items-center gap-2 mb-2 text-primary">
                                    <i class="bi bi-info-circle-fill fs-5"></i>
                                    <strong style="font-size: 0.88rem;">Tentang Radar Cuaca</strong>
                                </div>
                                <p class="text-secondary mb-0" style="font-size: 0.82rem; line-height: 1.5; text-align: justify;">
                                    Radar Cuaca Sidarma memantau pembentukan awan konvektif dan intensitas curah hujan secara real-time di wilayah Provinsi Gorontalo dan sekitarnya. Produk <strong>CMAX (Column Max)</strong> menampilkan reflektivitas maksimum per kolom atmosfer untuk mendeteksi potensi cuaca ekstrem, angin kencang, dan hujan es secara dini.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- TOMBOL KEMBALI -->
        <div>
            <a href="../index.php" class="btn btn-outline-secondary rounded-pill btn-sm px-4">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
            </a>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>