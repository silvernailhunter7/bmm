<?php 
require_once '../koneksi.php'; 

$page_title = "Tugas & Fungsi - BMKG Gorontalo";
$is_subpage = true;

include '../includes/header.php';
?>

<div class="container-fluid px-lg-5 my-4">
    <!-- BREADCRUMB NAVIGASI -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item">Profil</li>
            <li class="breadcrumb-item active">Tugas & Fungsi</li>
        </ol>
    </nav>

    <!-- HEADER HALAMAN -->
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-1">Tugas & Fungsi</h2>
        <p class="text-muted small">Tugas & Fungsi BMKG Provinsi Gorontalo</p>
        <hr class="mt-2 mb-4">
    </div>

    <!-- KARTU VISI -->
    <div class="card card-modern border rounded-4 shadow-sm p-4 mb-4 bg-white">
        <h5 class="fw-bold text-primary text-uppercase mb-3" style="letter-spacing: 0.5px;">KEDUDUKAN</h5>
            <p class="fst-italic text-secondary fs-5 mb-0" style="line-height: 1.6;">
                "Lorem Ipsum."
            </p>
        </blockquote>
    </div>

    <!-- KARTU VISI -->
    <div class="card card-modern border rounded-4 shadow-sm p-4 mb-4 bg-white">
        <h5 class="fw-bold text-primary text-uppercase mb-3" style="letter-spacing: 0.5px;">TUGAS POKOK</h5>
            <p class="fst-italic text-secondary fs-5 mb-0" style="line-height: 1.6;">
                "Lorem Ipsum."
            </p>
        </blockquote>
    </div>

    <!-- KARTU MISI -->
    <div class="card card-modern border rounded-4 shadow-sm p-4 mb-4 bg-white">
        <h5 class="fw-bold text-primary text-uppercase mb-3" style="letter-spacing: 0.5px;">FUNGSI</h5>
        <ol class="text-secondary lh-lg mb-0 ps-3" style="font-size: 0.98rem;">
            <li class="ps-2 mb-2">
                Lorem Ipsum.
            </li>
        </ol>
    </div>

    <!-- TOMBOL KEMBALI -->
    <div class="mt-4">
        <a href="../index.php" class="btn btn-outline-secondary rounded-pill btn-sm px-4">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>