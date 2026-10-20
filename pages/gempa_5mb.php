<?php 
require_once '../koneksi.php'; 
require_once '../includes/api_gempa.php';

$page_title = "Daftar Gempa > 5.0 M - BMKG Gorontalo";
$is_subpage = true;

// Ambil array list gempa M > 5.0 dari API BMKG gempaterkini.json
$list_gempa = get_bmkg_gempa('gempaterkini');

include '../includes/header.php';
?>

<div class="container-fluid px-lg-5 my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item">Geofisika</li>
            <li class="breadcrumb-item active">Gempa > 5.0 M</li>
        </ol>
    </nav>

    <div class="card card-modern p-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3 gap-2">
            <div>
                <h4 class="fw-bold mb-1 text-slate-800">
                    <i class="bi bi-activity text-danger me-2"></i>Daftar Gempa Bumi > 5.0 M Terkini (BMKG Geofisika)
                </h4>
                <p class="text-muted small mb-0">Daftar kejadian gempa signifikan dengan Magnitudo di atas 5.0 M di seluruh wilayah Indonesia.</p>
            </div>
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-dark text-nowrap">
                    <tr>
                        <th class="py-3 px-3">Waktu & Tanggal</th>
                        <th class="py-3 text-center">Magnitudo</th>
                        <th class="py-3 text-center">Kedalaman</th>
                        <th class="py-3">Lokasi Episentrum</th>
                        <th class="py-3 text-center">Potensi Tsunami</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($list_gempa) && is_array($list_gempa)): ?>
                        <?php foreach ($list_gempa as $g): ?>
                            <tr>
                                <td class="px-3 text-nowrap">
                                    <strong class="text-dark d-block"><?= htmlspecialchars($g['Jam']) ?></strong>
                                    <small class="text-muted"><?= htmlspecialchars($g['Tanggal']) ?></small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger fs-6 px-2.5 py-1.5 rounded-pill">
                                        <?= htmlspecialchars($g['Magnitude']) ?> M
                                    </span>
                                </td>
                                <td class="text-center text-nowrap fw-semibold text-secondary">
                                    <?= htmlspecialchars($g['Kedalaman']) ?>
                                </td>
                                <td class="fw-semibold text-dark">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                    <?= htmlspecialchars($g['Wilayah']) ?>
                                    <small class="d-block text-muted fw-normal" style="font-size: 0.75rem;">
                                        Koordinat: <?= htmlspecialchars($g['Coordinates'] ?? $g['Point']['coordinates'] ?? '-') ?>
                                    </small>
                                </td>
                                <td class="text-center text-nowrap">
                                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle rounded-pill px-3 py-1.5">
                                        <?= htmlspecialchars($g['Potensi'] ?? 'Tidak berpotensi Tsunami') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Gagal memuat data gempa dari server BMKG.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <a href="../index.php" class="btn btn-outline-secondary rounded-pill btn-sm px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>