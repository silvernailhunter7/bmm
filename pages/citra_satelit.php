<?php 
require_once '../koneksi.php'; 
$page_title = "Citra Satelit Cuaca - BMKG Gorontalo";
$is_subpage = true;
include '../includes/header.php';
?>

<div class="container px-lg-5 my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item">Meteorologi</li>
            <li class="breadcrumb-item active">Citra Satelit</li>
        </ol>
    </nav>

    <div class="card card-modern p-3">
    <div class="fw-bold mb-2 text-slate-800 d-flex justify-content-between align-items-center fs-6">
        <span><i class="bi bi-globe-americas text-primary me-2"></i>Animasi Satelit Himawari-9 Gorontalo</span>
    </div>
    
    <div class="text-center overflow-hidden p-2 rounded-3 bg-white border">
        <!-- Memanggil via Proxy PHP lokal -->
        <img src="../includes/proxy_gif.php" 
             referrerpolicy="no-referrer"
             class="img-fluid rounded-2 w-100" 
             alt="Animasi Satelit Himawari-9 Gorontalo" 
             style="max-height: 520px; object-fit: contain;">
    </div>
</div>

<?php include '../includes/footer.php'; ?>
