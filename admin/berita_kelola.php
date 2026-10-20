<?php
session_start();
require_once '../koneksi.php';

// Cek autentikasi admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$admin_user = $_SESSION['admin_username'] ?? 'admin';
$admin_role = $_SESSION['admin_role'] ?? 'all';

$success_msg = '';
$error_msg   = '';

// --- PROSES 1: AKSI HAPUS BERITA ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_hapus = (int)$_GET['id'];
    try {
        $stmt_del = $pdo->prepare("DELETE FROM berita WHERE id = :id");
        $stmt_del->execute([':id' => $id_hapus]);
        $success_msg = "Berita berhasil dihapus!";
    } catch (PDOException $e) {
        $error_msg = "Gagal menghapus berita: " . $e->getMessage();
    }
}

// --- PROSES 2: SIMPAN / INPUT BERITA BARU ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_berita'])) {
    $judul    = trim($_POST['judul'] ?? '');
    $kategori = trim($_POST['kategori'] ?? 'Meteorologi');
    $penulis  = trim($_POST['penulis'] ?? $admin_user);
    $isi      = trim($_POST['isi'] ?? '');
    $gambar_path = 'assets/img/default-news.jpg';

    if (!empty($judul) && !empty($isi)) {
        // Handle Upload Gambar
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $file_tmp  = $_FILES['gambar']['tmp_name'];
            $file_name = $_FILES['gambar']['name'];
            $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed   = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($ext, $allowed)) {
                $target_dir = '../uploads/';
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0755, true);
                }
                $new_name = 'news_' . time() . '_' . rand(100, 999) . '.' . $ext;
                $target_file = $target_dir . $new_name;

                if (move_uploaded_file($file_tmp, $target_file)) {
                    $gambar_path = 'uploads/' . $new_name;
                } else {
                    $error_msg = "Gagal mengunggah file gambar ke server.";
                }
            } else {
                $error_msg = "Format gambar tidak didukung. Gunakan JPG, PNG, WEBP, atau GIF.";
            }
        }

        // Insert ke Database
        if (empty($error_msg)) {
            try {
                $stmt_insert = $pdo->prepare("INSERT INTO berita (judul, kategori, penulis, isi, gambar, created_at) VALUES (:judul, :kategori, :penulis, :isi, :gambar, :created_at)");
                $stmt_insert->execute([
                    ':judul'      => $judul,
                    ':kategori'   => $kategori,
                    ':penulis'    => $penulis,
                    ':isi'        => $isi,
                    ':gambar'     => $gambar_path,
                    ':created_at' => date('Y-m-d H:i:s')
                ]);
                $success_msg = "Berita berhasil diterbitkan!";
            } catch (PDOException $e) {
                $error_msg = "Gagal menyimpan ke database: " . $e->getMessage();
            }
        }
    } else {
        $error_msg = "Judul dan Isi Berita wajib diisi.";
    }
}

// --- AMBIL SEMUA DAFTAR BERITA DITERBITKAN ---
$list_berita = [];
try {
    $stmt_fetch = $pdo->query("SELECT * FROM berita ORDER BY id DESC");
    $list_berita = $stmt_fetch->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $list_berita = [];
}

$page_title = "Kelola & Input Berita - BMKG Gorontalo";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }
        .sidebar { min-height: 100vh; background: #0f172a; color: #fff; }
        .sidebar .nav-link { color: #94a3b8; font-weight: 500; padding: 12px 20px; border-radius: 8px; margin-bottom: 4px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #0284c7; color: #fff; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        
        <!-- SIDEBAR NAVIGATION -->
        <div class="col-md-3 col-lg-2 sidebar p-3 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center gap-2 mb-4 px-2 pt-2">
                    <img src="https://www.bmkg.go.id/asset/img/logo/logo-bmkg.png" height="36" alt="BMKG">
                    <div>
                        <h6 class="fw-bold mb-0 text-white">PANEL ADMIN</h6>
                        <small class="text-info" style="font-size: 0.72rem;"><?= strtoupper($admin_user) ?></small>
                    </div>
                </div>

                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="berita_kelola.php"><i class="bi bi-newspaper me-2"></i> Kelola Berita</a>
                    </li>

                    <?php if ($admin_role === 'meteo' || $admin_role === 'all'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="cuaca_kelola.php"><i class="bi bi-cloud-sun me-2"></i> Data Cuaca ADM4</a>
                        </li>
                    <?php endif; ?>

                    <?php if ($admin_role === 'klima' || $admin_role === 'all'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="iklim_kelola.php"><i class="bi bi-calendar3 me-2"></i> Analisis Dasarian</a>
                        </li>
                    <?php endif; ?>

                    <?php if ($admin_role === 'geo' || $admin_role === 'all'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="gempa_kelola.php"><i class="bi bi-activity me-2"></i> Monitoring Gempa</a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item mt-3">
                        <a class="nav-link text-warning" href="change_password.php"><i class="bi bi-key me-2"></i> Ubah Password</a>
                    </li>
                </ul>
            </div>

            <div class="pt-3 border-top border-secondary">
                <a class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2" href="logout.php">
                    <i class="bi bi-box-arrow-right"></i> Keluar / Logout
                </a>
            </div>
        </div>

        <!-- CONTENT UTAMA -->
        <div class="col-md-9 col-lg-10 p-4">
            
            <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-3 shadow-sm border">
                <div>
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-newspaper text-primary me-2"></i>Manajemen Berita Resmi BMKG</h5>
                    <small class="text-muted">Kelola dan terbitkan berita, siaran pers, atau informasi meteorologi</small>
                </div>
                <a href="../index.php" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-globe me-1"></i> Lihat Web Portal
                </a>
            </div>

            <!-- NOTIFIKASI SUKSES / ERROR -->
            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success alert-dismissible fade show py-2.5 px-3 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i> <?= htmlspecialchars($success_msg) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger alert-dismissible fade show py-2.5 px-3 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= htmlspecialchars($error_msg) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- FORM INPUT BERITA BARU -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-primary text-white py-3 px-4">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Form Input Berita Baru</h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="row g-3">
                            
                            <div class="col-md-8">
                                <label for="judul" class="form-label fw-semibold text-dark small">Judul Berita</label>
                                <input type="text" name="judul" id="judul" class="form-control" placeholder="Masukkan judul berita..." required>
                            </div>

                            <div class="col-md-4">
                                <label for="kategori" class="form-label fw-semibold text-dark small">Kategori</label>
                                <select name="kategori" id="kategori" class="form-select">
                                    <option value="Meteorologi">Meteorologi</option>
                                    <option value="Klimatologi">Klimatologi</option>
                                    <option value="Geofisika">Geofisika</option>
                                    <option value="Siaran Pers">Siaran Pers</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="penulis" class="form-label fw-semibold text-dark small">Penulis / Kontributor</label>
                                <input type="text" name="penulis" id="penulis" class="form-control" value="Admin <?= ucfirst($admin_role) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="gambar" class="form-label fw-semibold text-dark small">Gambar Sampul Berita</label>
                                <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*">
                                <small class="text-muted" style="font-size: 0.75rem;">Format disarankan: JPG, PNG, WEBP (Maksimal 2MB)</small>
                            </div>

                            <div class="col-12">
                                <label for="isi" class="form-label fw-semibold text-dark small">Isi Konten Berita</label>
                                <textarea name="isi" id="isi" rows="6" class="form-control" placeholder="Tuliskan isi berita atau informasi resmi disini..." required></textarea>
                            </div>

                            <div class="col-12 pt-2">
                                <button type="submit" name="submit_berita" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold">
                                    <i class="bi bi-send-fill me-1"></i> Terbitkan Berita
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            <!-- TABEL DAFTAR BERITA DITERBITKAN -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-list-ul me-2"></i>Daftar Berita Diterbitkan</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width: 80px;">Gambar</th>
                                    <th>Judul Berita</th>
                                    <th style="width: 130px;">Kategori</th>
                                    <th style="width: 160px;">Penulis</th>
                                    <th style="width: 140px;">Tanggal</th>
                                    <th class="text-center pe-4" style="width: 90px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($list_berita)): ?>
                                    <?php foreach ($list_berita as $b): ?>
                                        <?php 
                                        $img_thumb = !empty($b['gambar']) ? '../' . $b['gambar'] : '../assets/img/default-news.jpg';
                                        ?>
                                        <tr>
                                            <td class="ps-4">
                                                <img src="<?= htmlspecialchars($img_thumb) ?>" 
                                                     class="rounded border object-fit-cover" 
                                                     style="width: 50px; height: 50px;" 
                                                     alt="Thumb"
                                                     onerror="this.src='https://www.bmkg.go.id/asset/img/logo/logo-bmkg.png';">
                                            </td>
                                            <td class="fw-bold text-dark">
                                                <?= htmlspecialchars($b['judul']) ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2.5 py-1">
                                                    <?= htmlspecialchars($b['kategori'] ?? 'Meteorologi') ?>
                                                </span>
                                            </td>
                                            <td class="text-muted"><?= htmlspecialchars($b['penulis'] ?? 'Admin BMKG') ?></td>
                                            <td class="text-muted"><?= date('d M Y H:i', strtotime($b['created_at'])) ?></td>
                                            <td class="text-center pe-4">
                                                <a href="?action=delete&id=<?= $b['id'] ?>" 
                                                   class="btn btn-outline-danger btn-sm rounded-circle p-1 px-2"
                                                   onclick="return confirm('Apakah Anda yakin ingin menghapus berita ini?');"
                                                   title="Hapus Berita">
                                                    <i class="bi bi-trash-fill"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Belum ada berita yang diunggah.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>