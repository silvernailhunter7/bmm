<?php 
require_once __DIR__ . '/../koneksi.php'; 

$page_title = "Layanan Pengaduan & WBS - BMKG Gorontalo";
$is_subpage = true;

include __DIR__ . '/../includes/header.php';
?>

<div class="container my-5 py-4">
    <!-- BREADCRUMB NAVIGASI -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item text-muted">Layanan Publik</li>
            <li class="breadcrumb-item active" aria-current="page">Pengaduan</li>
        </ol>
    </nav>

    <!-- CARD UTAMA PERINGATAN / REDIRECT LAPOR! -->
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7">
            <div class="card border-0 shadow-sm rounded-5 p-4 p-md-5 text-center bg-white">
                
                <!-- LOGO SP4N LAPOR! -->
                <div class="mb-4 d-flex justify-content-center align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <!-- Icon Chat Box Vektor -->
                        <div class="position-relative me-2" style="width: 48px; height: 42px;">
                            <i class="bi bi-chat-left-text-fill text-secondary fs-3 position-absolute top-0 start-0"></i>
                            <i class="bi bi-chat-right-text-fill text-danger fs-3 position-absolute bottom-0 end-0"></i>
                        </div>
                        <span class="fw-extrabold fs-2 tracking-tight text-danger" style="font-weight: 900; letter-spacing: -0.5px;">
                            LAPOR!
                        </span>
                    </div>
                </div>

                <!-- JUDUL SECTION -->
                <h3 class="fw-bold text-dark mb-3">Sampaikan Laporan Anda</h3>

                <!-- DESKRIPSI SINGKAT -->
                <p class="text-secondary fs-6 mb-4 px-md-3" style="line-height: 1.6;">
                    Anda akan dialihkan ke portal resmi SP4N-LAPOR! di <br class="d-none d-sm-inline">
                    <a href="https://www.lapor.go.id" target="_blank" class="text-decoration-none text-secondary fw-semibold">www.lapor.go.id</a>
                </p>

                <!-- TOMBOL REDIRECT MERAH -->
                <div class="mt-2">
                    <a href="https://www.lapor.go.id" target="_blank" rel="noopener noreferrer" class="btn btn-danger btn-lg rounded-pill px-4 py-3 fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="background-color: #ff2b38; border: none; font-size: 0.98rem;">
                        <span>Menuju Web SP4N LAPOR!</span>
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>