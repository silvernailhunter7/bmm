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
                    <i class="bi bi-broadcast text-primary me-2"></i>Citra Radar Cuaca Gorontalo (CMAX Sidarma)
                </h4>
                <p class="text-muted small mb-0">Deteksi dini intensitas hujan dan pergerakan sel awan Cumulonimbus (CB) secara presisi.</p>
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

            <!-- KOLOM KANAN: KETERANGAN INTENSITAS (PORSI 3/12) -->
            <div class="col-lg-3 col-xl-3 d-flex">
                <div class="card border bg-light p-3 rounded-3 w-100 d-flex flex-column justify-content-between">
                    <div>
                        <h6 class="fw-bold text-dark mb-3" style="font-size: 0.9rem;">Keterangan Intensitas Hujan (dBZ):</h6>
                        <ul class="list-unstyled small d-flex flex-column gap-2.5 mb-0" style="font-size: 0.82rem;">
                            <li class="p-2 bg-white rounded border d-flex align-items-center justify-content-between">
                                <span class="badge bg-info text-dark px-2 py-1">Light Blue</span>
                                <span class="text-muted fw-semibold">15 - 25 dBZ<br><small>(Hujan Ringan)</small></span>
                            </li>
                            <li class="p-2 bg-white rounded border d-flex align-items-center justify-content-between">
                                <span class="badge bg-primary px-2 py-1">Blue/Green</span>
                                <span class="text-muted fw-semibold">25 - 35 dBZ<br><small>(Hujan Sedang)</small></span>
                            </li>
                            <li class="p-2 bg-white rounded border d-flex align-items-center justify-content-between">
                                <span class="badge bg-warning text-dark px-2 py-1">Yellow/Orange</span>
                                <span class="text-muted fw-semibold">35 - 45 dBZ<br><small>(Hujan Lebat)</small></span>
                            </li>
                            <li class="p-2 bg-white rounded border d-flex align-items-center justify-content-between">
                                <span class="badge bg-danger px-2 py-1">Red/Purple</span>
                                <span class="text-muted fw-semibold">&gt; 45 dBZ<br><small>(Sangat Lebat/Es)</small></span>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="mt-3 pt-2 border-top text-center">
                        <small class="text-muted d-block mb-1" style="font-size: 0.72rem;">Format: CMAX Animasi</small>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.