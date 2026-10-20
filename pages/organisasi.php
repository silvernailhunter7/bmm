<?php 
require_once '../koneksi.php'; 

$page_title = "Struktur Organisasi - BMKG Gorontalo";
$is_subpage = true;

include '../includes/header.php';
?>

<div class="container-fluid px-lg-5 my-4">
    <!-- BREADCRUMB NAVIGASI -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item">Profil</li>
            <li class="breadcrumb-item active">Struktur Organisasi</li>
        </ol>
    </nav>

    <!-- HEADER HALAMAN -->
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-1 text-center">Bagan struktur organisasi Stasiun Meteorologi Kelas I Djalaluddin Gorontalo</h2>
        <hr class="mt-2 mb-4">
    </div>

    <!-- KARTU WADAH GAMBAR BAGAN STRUKTUR ORGANISASI -->
    <div class="card card-modern border rounded-4 shadow-sm p-3 p-md-4 mb-4 bg-white text-center">
        <div class="p-2 border rounded-3 bg-light d-inline-block w-100">
            <!-- Tempat Gambar Bagan (Tinggal ganti src 'assets/img/struktur_organisasi.png' dengan file PNG Anda) -->
            <img src="../assets/img/struktur_organisasi.png" 
                 class="img-fluid rounded-3 w-100" 
                 alt="Bagan Struktur Organisasi BMKG Gorontalo"
                 style="max-height: 850px; object-fit: contain;"
                 onerror="this.src='https://cdn.pixabay.com/photo/2014/06/03/19/38/board-361516_1280.jpg';">
        </div>
    </div>
        <!-- HEADER HALAMAN -->
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-1 text-center">Bagan struktur organisasi Stasiun Geofisika Kelas II Gorontalo</h2>
        <hr class="mt-2 mb-4">
    </div>

    <!-- KARTU WADAH GAMBAR BAGAN STRUKTUR ORGANISASI -->
    <div class="card card-modern border rounded-4 shadow-sm p-3 p-md-4 mb-4 bg-white text-center">
        <div class="p-2 border rounded-3 bg-light d-inline-block w-100">
            <!-- Tempat Gambar Bagan (Tinggal ganti src 'assets/img/struktur_organisasi.png' dengan file PNG Anda) -->
            <img src="../assets/img/struktur_organisasi.png" 
                 class="img-fluid rounded-3 w-100" 
                 alt="Bagan Struktur Organisasi BMKG Gorontalo"
                 style="max-height: 850px; object-fit: contain;"
                 onerror="this.src='https://cdn.pixabay.com/photo/2014/06/03/19/38/board-361516_1280.jpg';">
        </div>
    </div>
        <!-- HEADER HALAMAN -->
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-1 text-center">Bagan struktur organisasi Stasiun Klimatologi Kelas II Gorontalo</h2>
        <hr class="mt-2 mb-4">
    </div>

    <!-- KARTU WADAH GAMBAR BAGAN STRUKTUR ORGANISASI -->
    <div class="card card-modern border rounded-4 shadow-sm p-3 p-md-4 mb-4 bg-white text-center">
        <div class="p-2 border rounded-3 bg-light d-inline-block w-100">
            <!-- Tempat Gambar Bagan (Tinggal ganti src 'assets/img/struktur_organisasi.png' dengan file PNG Anda) -->
            <img src="../assets/img/struktur_organisasi.png" 
                 class="img-fluid rounded-3 w-100" 
                 alt="Bagan Struktur Organisasi BMKG Gorontalo"
                 style="max-height: 850px; object-fit: contain;"
                 onerror="this.src='https://cdn.pixabay.com/photo/2014/06/03/19/38/board-361516_1280.jpg';">
        </div>
    </div>

    <!-- TOMBOL KEMBALI -->
    <div class="mt-4">
        <a href="../index.php" class="btn btn-outline-secondary rounded-pill btn-sm px-4">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>