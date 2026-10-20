<?php 
require_once '../koneksi.php'; 

$page_title = "Sejarah - BMKG Gorontalo";
$is_subpage = true;

include '../includes/header.php';
?>

<div class="container-fluid px-lg-5 my-4">
    <!-- BREADCRUMB NAVIGASI -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item">Profil</li>
            <li class="breadcrumb-item active">Sejarah</li>
        </ol>
    </nav>

    <!-- HEADER HALAMAN -->
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-1">Sejarah</h2>
        <p class="text-muted small">Sejarah BMKG Provinsi Gorontalo</p>
        <hr class="mt-2 mb-4">
    </div>

    <!-- KARTU VISI -->
    <div class="card card-modern border rounded-4 shadow-sm p-4 mb-4 bg-white">
            <p class="fst-italic text-secondary fs-5 mb-0" style="line-height: 1.6;">
                "Sejarah."
            </p>
        </blockquote>
    </div>

    <!-- TOMBOL KEMBALI -->
    <div class="mt-4">
        <a href="../index.php" class="btn btn-outline-secondary rounded-pill btn-sm px-4">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>