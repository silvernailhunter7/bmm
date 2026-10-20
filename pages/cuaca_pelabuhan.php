<?php 
require_once '../koneksi.php'; 

$page_title = "Informasi Cuaca Pelabuhan & Maritim - BMKG Gorontalo";
$is_subpage = true;

// Fetch Data Cuaca Pelabuhan
$list_media = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM cuaca_penerbangan_pelabuhan WHERE kategori = 'pelabuhan' ORDER BY id DESC");
    $stmt->execute();
    $list_media = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $list_media = [];
}

include '../includes/header.php';
?>

<div class="container-fluid px-lg-5 my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item">Meteorologi</li>
            <li class="breadcrumb-item active">Cuaca Pelabuhan</li>
        </ol>
    </nav>

    <div class="card card-modern p-4 border rounded-4 shadow-sm bg-white">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
            <div>
                <h3 class="fw-bold text-dark mb-1">
                    <i class="bi bi-tsunami text-info me-2"></i>Informasi Cuaca Pelabuhan & Maritim
                </h3>
                <p class="text-muted small mb-0">Rilis visual prakiraan cuaca maritim dan pelabuhan wilayah Provinsi Gorontalo.</p>
            </div>
            <a href="cuaca_penerbangan.php" class="btn btn-outline-primary rounded-pill btn-sm px-3">
                <i class="bi bi-airplane me-1"></i> Lihat Cuaca Penerbangan
            </a>
        </div>

        <!-- SLIDER ANIMASI OTOMATIS 5 DETIK -->
        <?php if (!empty($list_media)): ?>
            <div id="carouselPelabuhan" class="carousel slide rounded-4 overflow-hidden border bg-dark shadow-sm" data-bs-ride="carousel" data-bs-interval="5000">
                <?php if (count($list_media) > 1): ?>
                    <div class="carousel-indicators mb-2">
                        <?php foreach ($list_media as $idx => $m): ?>
                            <button type="button" data-bs-target="#carouselPelabuhan" data-bs-slide-to="<?= $idx ?>" class="<?= $idx === 0 ? 'active' : '' ?>"></button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="carousel-inner">
                    <?php foreach ($list_media as $idx => $m): ?>
                        <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>" data-bs-interval="5000">
                            <div class="p-2 text-center">
                                <img src="../<?= htmlspecialchars($m['gambar_path']) ?>?t=<?= time() ?>" 
                                     class="img-fluid rounded-3" 
                                     alt="<?= htmlspecialchars($m['judul']) ?>" 
                                     style="max-height: 780px; width: 100%; object-fit: contain;">
                            </div>
                            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-75 rounded-3 px-3 py-1 mb-2 mx-auto" style="max-width: 600px;">
                                <small class="fw-semibold text-white"><?= htmlspecialchars($m['judul']) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (count($list_media) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselPelabuhan" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon bg-dark rounded-circle p-2"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselPelabuhan" data-bs-slide="next">
                        <span class="carousel-control-next-icon bg-dark rounded-circle p-2"></span>
                    </button>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5 border rounded-4 bg-light">
                <i class="bi bi-water text-muted fs-1 d-block mb-2"></i>
                <p class="text-muted mb-0">Belum ada media informasi cuaca pelabuhan yang diunggah.</p>
            </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="../index.php" class="btn btn-outline-secondary rounded-pill btn-sm px-4">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>