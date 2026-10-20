<?php 
require_once '../koneksi.php'; 

$page_title = "Transparansi Kinerja - BMKG Gorontalo";
$is_subpage = true;

include '../includes/header.php';
?>

<style>
    /* STYLING CARD BUBBLE TRANSPARANSI (NON-CLICKABLE) */
    .card-transparansi {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 24px 28px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        display: flex;
        align-items: center;
        gap: 20px;
        user-select: none; /* Mencegah teks terpilih secara tak sengaja */
        cursor: default;   /* Pointer kursor tetap standar panah, bukan hand/link */
    }

    .icon-bubble {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background-color: #e0f2fe;
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .title-transparansi {
        font-weight: 700;
        color: #1e293b;
        font-size: 1.05rem;
        margin-bottom: 4px;
        line-height: 1.3;
    }

    .text-lihat-dokumen {
        color: #0284c7;
        font-size: 0.88rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
</style>

<div class="container-fluid px-lg-5 my-4">
    <!-- BREADCRUMB -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item">Publik</li>
            <li class="breadcrumb-item active">Transparansi Kinerja</li>
        </ol>
    </nav>

    <div class="card card-modern p-4 p-md-5 border rounded-4 shadow-sm bg-white">
        
        <!-- HEADER TRANSPARANSI KINERJA -->
        <div class="mb-4 pb-3 border-bottom">
            <h2 class="fw-bold text-dark mb-2">Transparansi Kinerja</h2>
            <p class="text-muted mb-0" style="font-size: 0.98rem; max-width: 850px; line-height: 1.6;">
                Bentuk komitmen BMKG Gorontalo dalam mewujudkan tata kelola pemerintahan yang baik melalui keterbukaan informasi publik.
            </p>
        </div>

        <!-- GRID BUBBLE DOKUMEN (NON-CLICKABLE) -->
        <div class="row g-4 mb-4">
            
            <!-- BUBBLE 1 -->
            <div class="col-md-6">
                <div class="card-transparansi">
                    <div class="icon-bubble">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div>
                        <div class="title-transparansi">Daftar Informasi Publik (DIP) 2024</div>
                        <span class="text-lihat-dokumen">Lihat Dokumen <i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i></span>
                    </div>
                </div>
            </div>

            <!-- BUBBLE 2 -->
            <div class="col-md-6">
                <div class="card-transparansi">
                    <div class="icon-bubble">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div>
                        <div class="title-transparansi">Laporan Kinerja Instansi Pemerintah (LKIP) 2023</div>
                        <span class="text-lihat-dokumen">Lihat Dokumen <i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i></span>
                    </div>
                </div>
            </div>

            <!-- BUBBLE 3 -->
            <div class="col-md-6">
                <div class="card-transparansi">
                    <div class="icon-bubble">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div>
                        <div class="title-transparansi">Rencana Strategis (Renstra) BMKG 2020–2024</div>
                        <span class="text-lihat-dokumen">Lihat Dokumen <i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i></span>
                    </div>
                </div>
            </div>

            <!-- BUBBLE 4 -->
            <div class="col-md-6">
                <div class="card-transparansi">
                    <div class="icon-bubble">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div>
                        <div class="title-transparansi">Standar Operasional Prosedur (SOP) Pelayanan Publik</div>
                        <span class="text-lihat-dokumen">Lihat Dokumen <i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i></span>
                    </div>
                </div>
            </div>

            <!-- BUBBLE 5 -->
            <div class="col-md-6">
                <div class="card-transparansi">
                    <div class="icon-bubble">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div>
                        <div class="title-transparansi">Standar Operasional Prosedur (SOP) Pelayanan Publik (Dokumen Geof)</div>
                        <span class="text-lihat-dokumen">Lihat Dokumen <i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i></span>
                    </div>
                </div>
            </div>

            <!-- BUBBLE 6 -->
            <div class="col-md-6">
                <div class="card-transparansi">
                    <div class="icon-bubble">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div>
                        <div class="title-transparansi">Standar Operasional Prosedur (SOP) Pelayanan Publik (Dokumen Klimat)</div>
                        <span class="text-lihat-dokumen">Lihat Dokumen <i class="bi bi-chevron-right" style="font-size: 0.75rem;"></i></span>
                    </div>
                </div>
            </div>

        </div>

        <!-- TOMBOL KEMBALI -->
        <div class="pt-2">
            <a href="../index.php" class="btn btn-outline-secondary rounded-pill btn-sm px-4">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
            </a>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>