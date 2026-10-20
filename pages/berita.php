<?php 
require_once '../koneksi.php'; 

$page_title = "Arsip Berita & Informasi Resmi - BMKG Gorontalo";
$is_subpage = true;

// KONFIGURASI PAGINATION (MAKS 4 BERITA PER PAGE)
$limit = 4;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// HITUNG TOTAL BERITA
$total_berita = 0;
try {
    $stmt_total = $pdo->query("SELECT COUNT(*) FROM berita");
    $total_berita = $stmt_total->fetchColumn();
} catch (PDOException $e) {
    $total_berita = 0;
}
$total_pages = ceil($total_berita / $limit);

// AMBIL BERITA UNTUK PAGE AKTIF
$berita_list = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM berita ORDER BY id DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $berita_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $berita_list = [];
}

include '../includes/header.php';
?>

<div class="container-fluid px-lg-5 my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item active">Arsip Berita</li>
        </ol>
    </nav>

    <div class="card card-modern p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-slate-800"><i class="bi bi-newspaper text-primary me-2"></i>Arsip Berita & Informasi Resmi BMKG</h4>
                <p class="text-muted small mb-0">Daftar publikasi berita meteorologi, klimatologi, dan geofisika Provinsi Gorontalo</p>
            </div>
            <a href="../index.php" class="btn btn-outline-secondary rounded-pill btn-sm px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <?php if (!empty($berita_list)): ?>
            <div class="row g-4">
                <?php foreach ($berita_list as $b): ?>
                    <?php 
                    $img_src = !empty($b['gambar']) ? $b['gambar'] : '../assets/img/default-news.jpg';
                    if (!preg_match("~^(?:f|ht)tps?://~i", $img_src) && !file_exists('../' . $img_src)) {
                        $img_src = '../uploads/' . basename($img_src);
                    } else if (!preg_match("~^(?:f|ht)tps?://~i", $img_src)) {
                        $img_src = '../' . $img_src;
                    }
                    $judul    = $b['judul'] ?? 'Berita BMKG';
                    $kategori = $b['kategori'] ?? 'Meteorologi';
                    $isi      = $b['isi'] ?? $b['konten'] ?? $b['deskripsi'] ?? '';
                    $penulis  = $b['penulis'] ?? $b['author'] ?? 'Admin BMKG';
                    $tanggal  = $b['created_at'] ?? $b['tgl_terbit'] ?? date('Y-m-d');
                    ?>
                    <div class="col-md-6">
                        <div class="card h-100 border shadow-sm rounded-3 overflow-hidden">
                            <img src="<?= htmlspecialchars($img_src) ?>" 
                                 class="card-img-top object-fit-cover" 
                                 style="height: 200px;" 
                                 alt="Gambar Berita"
                                 onerror="this.src='https://www.bmkg.go.id/asset/img/logo/logo-bmkg.png';">
                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <span class="badge bg-primary rounded-pill mb-2"><?= htmlspecialchars($kategori) ?></span>
                                    <h5 class="fw-bold text-dark fs-6 mb-2"><?= htmlspecialchars($judul) ?></h5>
                                    <p class="text-muted small mb-3" style="font-size: 0.85rem;">
                                        <?= htmlspecialchars(substr(strip_tags($isi), 0, 130)) ?>...
                                    </p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center text-muted border-top pt-2" style="font-size: 0.78rem;">
                                    <span><i class="bi bi-calendar3 me-1"></i><?= date('d M Y', strtotime($tanggal)) ?></span>
                                    <span><i class="bi bi-person me-1"></i><?= htmlspecialchars($penulis) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- KOMPONEN PAGINATION -->
            <?php if ($total_pages > 1): ?>
                <nav class="mt-4 pt-2">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>">Sebelumnya</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>">Selanjutnya</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <div class="text-center py-5">
                <p class="text-muted mb-0">Belum ada berita yang tersedia.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>