<?php 
require_once '../koneksi.php'; 

$page_title = "Analisis & Informasi Iklim Dasarian - BMKG Gorontalo";
$is_subpage = true;

// Ambil Data Iklim Dasarian Terbaru
$data_dasarian = null;
try {
    $stmt = $pdo->query("SELECT * FROM iklim_dasarian ORDER BY id DESC LIMIT 1");
    $data_dasarian = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
} catch (PDOException $e) {
    $data_dasarian = null;
}

$tgl_awal  = !empty($data_dasarian['tgl_awal']) ? $data_dasarian['tgl_awal'] : date('Y-m-01');
$tgl_akhir = !empty($data_dasarian['tgl_akhir']) ? $data_dasarian['tgl_akhir'] : date('Y-m-10');
$tahun     = !empty($data_dasarian['tahun']) ? $data_dasarian['tahun'] : date('Y');
$deskripsi = !empty($data_dasarian['deskripsi']) ? $data_dasarian['deskripsi'] : 'Analisis dan prakiraan curah hujan dasarian wilayah Provinsi Gorontalo.';
$gambar    = !empty($data_dasarian['gambar_peta']) ? '../' . $data_dasarian['gambar_peta'] : 'https://www.bmkg.go.id/asset/img/logo/logo-bmkg.png';

// Format Tanggal Indonesia (Contoh: 01 s/d 10 Oktober 2026)
$time_awal  = strtotime($tgl_awal);
$time_akhir = strtotime($tgl_akhir);
$tgl_range_str = date('d', $time_awal) . ' s/d ' . date('d F Y', $time_akhir);

include '../includes/header.php';
?>

<div class="container-fluid px-lg-5 my-4">
    <!-- BREADCRUMB -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item">Klimatologi</li>
            <li class="breadcrumb-item active">Analisis Iklim Dasarian</li>
        </ol>
    </nav>

    <div class="card card-modern p-4 border rounded-4 shadow-sm bg-white">
        
        <!-- HEADER HALAMAN -->
        <div class="d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-calendar-range-fill text-success fs-3"></i>
            <h3 class="fw-bold text-dark mb-0">Analisis & Prakiraan Iklim Dasarian</h3>
        </div>
        <p class="text-muted small mb-4">Laporan, peta analisis, dan prakiraan curah hujan dasarian (10 harian) wilayah Provinsi Gorontalo.</p>

        <!-- KOTAK PRAKIRAAN RINGKAS DENGAN RENTANG TANGGAL -->
        <div class="alert border-0 rounded-4 p-3 mb-4 d-flex align-items-start gap-2.5" style="background-color: #d1fae5; color: #065f46;">
            <i class="bi bi-info-circle-fill fs-5 flex-shrink-0 text-success"></i>
            <div style="font-size: 0.95rem; line-height: 1.6;">
                <strong>Periode Dasarian:</strong> <?= htmlspecialchars($tgl_range_str) ?>: <?= htmlspecialchars($deskripsi) ?>
            </div>
        </div>

        <!-- WADAH GAMBAR PETA ANALISIS DASARIAN -->
        <div class="card border-0 rounded-4 overflow-hidden mb-4 bg-dark">
            <div class="card-header bg-dark text-white border-secondary py-2.5 px-3 d-flex justify-content-between align-items-center">
                <small class="fw-semibold"><i class="bi bi-image me-1.5"></i> Peta Analisis Curah Hujan & Iklim BMKG Gorontalo</small>
                <small class="text-muted" style="font-size: 0.75rem;">Periode: <?= htmlspecialchars($tgl_range_str) ?></small>
            </div>
            <div class="card-body p-2 text-center">
                <img src="<?= htmlspecialchars($gambar) ?>?t=<?= time() ?>" 
                     class="img-fluid rounded-3 w-100" 
                     alt="Peta Analisis Iklim Dasarian"
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