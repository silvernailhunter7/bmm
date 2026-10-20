<?php 
require_once '../koneksi.php'; 

$page_title = "Analisis & Prakiraan Musim Hujan Per-Semester - BMKG Gorontalo";
$is_subpage = true;

// Ambil Data Iklim Semester Terbaru
$data_iklim = null;
try {
    $stmt = $pdo->query("SELECT * FROM iklim_semester ORDER BY id DESC LIMIT 1");
    $data_iklim = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $data_iklim = null;
}

$semester  = $data_iklim['semester'] ?? 'Semester II';
$tahun     = $data_iklim['tahun'] ?? date('Y');
$deskripsi = $data_iklim['deskripsi'] ?? 'Puncak musim hujan diperkirakan terjadi pada bulan November - Desember dengan curah hujan rata-rata berkisar antara 200 - 350 mm/bulan.';
$gambar    = !empty($data_iklim['gambar_peta']) ? '../' . $data_iklim['gambar_peta'] : 'https://www.bmkg.go.id/asset/img/logo/logo-bmkg.png';

include '../includes/header.php';
?>

<div class="container-fluid px-lg-5 my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item">Klimatologi</li>
            <li class="breadcrumb-item active">Analisis & Prakiraan Musim</li>
        </ol>
    </nav>

    <div class="card card-modern p-4 border rounded-4 shadow-sm bg-white">
        
        <!-- HEADER HALAMAN -->
        <div class="d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-bar-chart-line-fill text-primary fs-3"></i>
            <h3 class="fw-bold text-dark mb-0">Analisis & Prakiraan Musim Hujan Per-Semester</h3>
        </div>
        <p class="text-muted small mb-4">Laporan, peta analisis, dan prakiraan curah hujan serta kondisi iklim wilayah Provinsi Gorontalo.</p>

        <!-- KOTAK PRAKIRAAN RINGKAS (DINAMIS DARI ADMIN) -->
        <div class="alert alert-primary border-0 rounded-4 p-3 mb-4 text-primary-emphasis d-flex align-items-start gap-2.5" style="background-color: #dbeafe;">
            <i class="bi bi-info-circle-fill fs-5 flex-shrink-0 text-primary"></i>
            <div style="font-size: 0.95rem; line-height: 1.6;">
                <strong>Prakiraan Ringkas:</strong> <?= htmlspecialchars($semester) ?> - <?= htmlspecialchars($tahun) ?>: <?= htmlspecialchars($deskripsi) ?>
            </div>
        </div>

        <!-- WADAH GAMBAR PETA ANALISIS (DINAMIS DARI ADMIN) -->
        <div class="card border-0 rounded-4 overflow-hidden mb-4 bg-dark">
            <div class="card-header bg-dark text-white border-secondary py-2.5 px-3">
                <small class="fw-semibold"><i class="bi bi-image me-1.5"></i> Peta Analisis Curah Hujan & Iklim BMKG Gorontalo</small>
            </div>
            <div class="card-body p-2 text-center">
                <img src="<?= htmlspecialchars($gambar) ?>" 
                     class="img-fluid rounded-3 w-100" 
                     alt="Peta Analisis Iklim"
                     style="max-height: 750px; object-fit: contain;"
                     onerror="this.src='https://www.bmkg.go.id/asset/img/logo/logo-bmkg.png';">
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