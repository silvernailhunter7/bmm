<?php 
require_once __DIR__ . '/../koneksi.php'; 

$page_title = "Kritik dan Saran - BMKG Gorontalo";
$is_subpage = true;

// UBAH LINK FORMULIR (EMBED/IFRAME) DI SINI
$form_url = "https://forms.zohopublic.com/your_account/form/KritikdanSaran/formperma/your_form_id";

include __DIR__ . '/../includes/header.php';
?>

<div class="container my-4 py-2">
    <!-- BREADCRUMB NAVIGASI -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item text-muted">Layanan Publik</li>
            <li class="breadcrumb-item active" aria-current="page">Kritik dan Saran</li>
        </ol>
    </nav>

    <!-- HEADER SECTION -->
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-1">Kritik dan Saran</h2>
        <p class="text-muted">Sampaikan masukan Anda untuk perbaikan layanan BMKG Gorontalo</p>
    </div>

    <!-- KONTANER EMBED FORMULIR -->
    <div class="card card-modern border shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-header bg-white border-bottom py-3 px-4">
            <small class="fw-bold text-muted text-uppercase tracking-wider">KRITIK DAN SARAN</small>
        </div>
        <div class="card-body p-0">
            <div class="ratio ratio-4x3" style="min-height: 680px;">
                <iframe 
                    src="<?= htmlspecialchars($form_url) ?>" 
                    title="Formulir Kritik dan Saran BMKG" 
                    frameborder="0" 
                    scrolling="yes"
                    allowfullscreen>
                </iframe>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>