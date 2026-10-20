<?php 
require_once '../koneksi.php'; 

$page_title = "Siaran Pers Cuaca Ekstrem - BMKG Gorontalo";
$is_subpage = true;

// Ambil Data Siaran Pers Terbaru
$data_pers = null;
try {
    $stmt = $pdo->query("SELECT * FROM siaran_pers ORDER BY id DESC LIMIT 1");
    $data_pers = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $data_pers = null;
}

$judul     = $data_pers['judul'] ?? 'SIARAN PERS: Potensi Cuaca Ekstrem dan Hujan Lebat di Wilayah Provinsi Gorontalo';
$tanggal   = $data_pers['tanggal_rilis'] ?? date('Y-m-d');
$deskripsi = $data_pers['deskripsi'] ?? 'Masyarakat dihimbau agar tetap waspada terhadap potensi bencana hidrometeorologi seperti banjir dan tanah longsor.';
$gambar    = !empty($data_pers['gambar_dokumen']) ? '../' . $data_pers['gambar_dokumen'] : 'https://www.bmkg.go.id/asset/img/logo/logo-bmkg.png';

include '../includes/header.php';
?>

<div class="container-fluid px-lg-5 my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item">Berita</li>
            <li class="breadcrumb-item active">Siaran Pers</li>
        </ol>
    </nav>

    <div class="card card-modern p-4 border rounded-4 shadow-sm bg-white">
        
        <!-- HEADER HALAMAN -->
        <div class="d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-broadcast text-danger fs-3"></i>
            <h3 class="fw-bold text-dark mb-0">Siaran Pers Resmi BMKG Gorontalo</h3>
        </div>
        <p class="text-muted small mb-4">Informasi rilis resmi dinamika atmosfer dan himbauan kewaspadaan cuaca ekstrem.</p>

        <!-- KARTU UTAMA SIARAN PERS -->
        <div class="border rounded-4 p-4 mb-4 bg-light">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge bg-danger rounded-pill px-3 py-1.5 fw-bold">SIARAN PERS METEOROLOGI</span>
                <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i> Ditertbitkan: <?= date('d F Y', strtotime($tanggal)) ?></span>
            </div>

            <h4 class="fw-bold text-dark mb-3"><?= htmlspecialchars($judul) ?></h4>
            
            <div class="bg-white p-3 rounded-3 border mb-4 text-secondary" style="line-height: 1.7; font-size: 0.96rem;">
                <?= nl2br(htmlspecialchars($deskripsi)) ?>
            </div>

            <!-- DISPLAY DOKUMEN / PETA LAMPIRAN -->
            <div class="card border-0 rounded-4 overflow-hidden bg-dark">
                <div class="card-header bg-dark text-white border-secondary py-2.5 px-3">
                    <small class="fw-semibold"><i class="bi bi-file-earmark-image me-1.5"></i> Lampiran Dokumen Resmi / Peta Cuaca Ekstrem</small>
                </div>
                <div class="card-body p-2 text-center">
                    <img src="<?= htmlspecialchars($gambar) ?>" 
                         class="img-fluid rounded-3 w-100" 
                         alt="Dokumen Siaran Pers"
                         style="max-height: 800px; object-fit: contain;"
                         onerror="this.src='https://www.bmkg.go.id/asset/img/logo/logo-bmkg.png';">
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