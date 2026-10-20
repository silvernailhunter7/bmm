<?php
session_start();
require_once '../koneksi.php';

// 1. Cek Autentikasi Admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$admin_user = $_SESSION['admin_username'] ?? 'admin';
$admin_role = strtolower($_SESSION['admin_role'] ?? 'all');

// 2. PENGECEKAN RBAC FLEKSIBEL (Hanya admin_meteo, meteorologi, atau super admin all)
if (strpos($admin_role, 'meteo') === false && $admin_role !== 'all') {
    die("<div style='padding:30px; font-family:sans-serif;'>
            <h3 style='color:red;'>Akses Ditolak!</h3>
            <p>Halaman ini hanya dapat diakses oleh <strong>Admin Meteorologi</strong>.</p>
            <a href='index.php'>Kembali ke Dashboard Admin</a>
         </div>");
}

$success_msg = '';
$error_msg   = '';

// 3. Buat / Merekondisi Tabel siaran_pers
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS siaran_pers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        judul VARCHAR(255) NOT NULL DEFAULT 'SIARAN PERS: Potensi Cuaca Ekstrem',
        tanggal_rilis DATE NOT NULL,
        deskripsi TEXT NOT NULL,
        gambar_dokumen VARCHAR(255) NOT NULL DEFAULT 'assets/img/default-siaran-pers.jpg',
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    // Abaikan jika tabel sudah ada
}

// 4. AMBIL DATA SIARAN PERS TERBARU UNTUK DEFAULT VALUE
$data_pers = null;
try {
    $stmt_get = $pdo->query("SELECT * FROM siaran_pers ORDER BY id DESC LIMIT 1");
    $data_pers = $stmt_get ? $stmt_get->fetch(PDO::FETCH_ASSOC) : null;
} catch (PDOException $e) {
    $data_pers = null;
}

$def_judul     = !empty($data_pers['judul']) ? $data_pers['judul'] : 'SIARAN PERS: Potensi Cuaca Ekstrem dan Hujan Lebat di Wilayah Provinsi Gorontalo';
$def_tanggal   = !empty($data_pers['tanggal_rilis']) ? $data_pers['tanggal_rilis'] : date('Y-m-d');
$def_deskripsi = !empty($data_pers['deskripsi']) ? $data_pers['deskripsi'] : 'Masyarakat dihimbau agar tetap waspada terhadap potensi bencana hidrometeorologi seperti banjir dan tanah longsor.';
$def_gambar    = !empty($data_pers['gambar_dokumen']) ? $data_pers['gambar_dokumen'] : 'assets/img/default-siaran-pers.jpg';

// 5. PROSES SIMPAN / UPDATE DATA SIARAN PERS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Cek apakah POST kosong akibat ukuran file melebihi post_max_size PHP
    if (empty($_POST) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
        $error_msg = "Ukuran file dokumen yang diunggah melebihi batas maksimum server. Silakan pilih file berukuran lebih kecil (di bawah 10MB).";
    } else {
        // AMBIL INPUT DENGAN GARANSI TIDAK PERNAH NULL
        $judul         = (!empty($_POST['judul'])) ? trim($_POST['judul']) : $def_judul;
        $tanggal_rilis = (!empty($_POST['tanggal_rilis'])) ? trim($_POST['tanggal_rilis']) : $def_tanggal;
        $deskripsi     = (!empty($_POST['deskripsi'])) ? trim($_POST['deskripsi']) : $def_deskripsi;
        
        $gambar_path = $def_gambar; // Default gambar lama

        // HANDLE UPLOAD DOKUMEN / GAMBAR (Max 10MB)
        if (isset($_FILES['gambar_dokumen']) && $_FILES['gambar_dokumen']['error'] === UPLOAD_ERR_OK) {
            $file_tmp  = $_FILES['gambar_dokumen']['tmp_name'];
            $file_name = $_FILES['gambar_dokumen']['name'];
            $file_size = $_FILES['gambar_dokumen']['size'];
            $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg', 'tiff', 'pdf'];
            $max_size    = 10 * 1024 * 1024; // 10MB

            if ($file_size > $max_size) {
                $error_msg = "Ukuran file terlalu besar! Maksimal kapasitas file adalah 10MB.";
            } elseif (in_array($ext, $allowed_ext)) {
                $target_dir = '../uploads/';
                if (!is_dir($target_dir)) {
                    @mkdir($target_dir, 0777, true);
                }

                $new_name    = 'press_release_' . time() . '_' . rand(1000, 9999) . '.' . ($ext ?: 'png');
                $target_file = $target_dir . $new_name;

                if (move_uploaded_file($file_tmp, $target_file)) {
                    $gambar_path = 'uploads/' . $new_name;
                } else {
                    $error_msg = "Gagal memindahkan file terunggah ke folder server.";
                }
            } else {
                $error_msg = "Format file tidak didukung. Gunakan PNG, JPG, JPEG, GIF, WEBP, BMP, SVG, atau PDF.";
            }
        } elseif (isset($_FILES['gambar_dokumen']) && $_FILES['gambar_dokumen']['error'] === UPLOAD_ERR_INI_SIZE) {
            $error_msg = "File dokumen terlalu besar untuk batas upload PHP server. Gunakan file berukuran lebih kecil.";
        } elseif (isset($_FILES['gambar_dokumen']) && $_FILES['gambar_dokumen']['error'] !== UPLOAD_ERR_NO_FILE) {
            $error_msg = "Gagal mengunggah file. Kode Error PHP: " . $_FILES['gambar_dokumen']['error'];
        }

        // PROSES INSERT KE DATABASE DENGAN JAMINAN TANPA ERROR NULL
        if (empty($error_msg)) {
            try {
                $stmt_inst = $pdo->prepare("INSERT INTO siaran_pers (judul, tanggal_rilis, deskripsi, gambar_dokumen, updated_at) VALUES (:judul, :tgl, :deskripsi, :gambar, :updated_at)");
                $stmt_inst->execute([
                    ':judul'      => (string)$judul,
                    ':tgl'        => (string)$tanggal_rilis,
                    ':deskripsi'  => (string)$deskripsi,
                    ':gambar'     => (string)$gambar_path,
                    ':updated_at' => date('Y-m-d H:i:s')
                ]);
                
                $success_msg = "Dokumen Siaran Pers Cuaca Ekstrem berhasil diterbitkan!";
                
                // Refresh variabel display
                $def_judul     = $judul;
                $def_tanggal   = $tanggal_rilis;
                $def_deskripsi = $deskripsi;
                $def_gambar    = $gambar_path;
            } catch (PDOException $e) {
                $error_msg = "Gagal menyimpan ke database: " . $e->getMessage();
            }
        }
    }
}

$display_gambar_src = (strpos($def_gambar, 'http') === 0) ? $def_gambar : '../' . $def_gambar;
$page_title = "Kelola Siaran Pers Cuaca Ekstrem - Panel Admin";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
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
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link text-info fw-semibold" href="pers.php"><i class="bi bi-file-earmark-text me-2"></i> Siaran Pers Meteo</a></li>
                    <li class="nav-item"><a class="nav-link" href="penerbangan.php"><i class="bi bi-airplane me-2"></i> Cuaca Penerbangan</a></li>
                    <li class="nav-item"><a class="nav-link" href="maritim.php"><i class="bi bi-tsunami me-2"></i> Cuaca Pelabuhan</a></li>
                </ul>
            </div>
            <div class="pt-3 border-top border-secondary"><a class="btn btn-outline-danger w-100" href="logout.php"><i class="bi bi-box-arrow-right me-1"></i> Logout</a></div>
        </div>

        <!-- MAIN CONTENT AREA -->
        <div class="col-md-9 col-lg-10 p-4">
            
            <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-3 shadow-sm border">
                <div>
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-broadcast text-warning me-2"></i>Kelola Siaran Pers Cuaca Ekstrem</h5>
                    <small class="text-muted">Fitur khusus Admin Meteorologi (Stamet Djalaluddin Gorontalo)</small>
                </div>
                <a href="../pages/siaran_pers.php" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-eye me-1"></i> Lihat Tampilan Publik
                </a>
            </div>

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

            <!-- FORM UPDATE SIARAN PERS -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-warning text-dark py-3 px-4">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Form Upload Dokumen Siaran Pers Cuaca Ekstrem</h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="row g-3">
                            
                            <div class="col-md-8">
                                <label for="judul" class="form-label fw-semibold text-dark small">Judul Siaran Pers</label>
                                <input type="text" name="judul" id="judul" class="form-control" value="<?= htmlspecialchars($def_judul) ?>" placeholder="Contoh: SIARAN PERS POTENSI CUACA EKSTREM..." required>
                            </div>

                            <div class="col-md-4">
                                <label for="tanggal_rilis" class="form-label fw-semibold text-dark small">Tanggal Rilis</label>
                                <input type="date" name="tanggal_rilis" id="tanggal_rilis" class="form-control" value="<?= htmlspecialchars($def_tanggal) ?>" required>
                            </div>

                            <div class="col-12">
                                <label for="deskripsi" class="form-label fw-semibold text-dark small">Deskripsi / Narasi Peringatan Dini</label>
                                <textarea name="deskripsi" id="deskripsi" rows="5" class="form-control" placeholder="Tuliskan poin-poin himbauan atau dinamika atmosfer..." required><?= htmlspecialchars($def_deskripsi) ?></textarea>
                            </div>

                            <div class="col-12">
                                <label for="gambar_dokumen" class="form-label fw-semibold text-dark small">Upload Gambar Dokumen Siaran Pers / Peta Cuaca Ekstrem</label>
                                <input type="file" name="gambar_dokumen" id="gambar_dokumen" class="form-control" accept="image/*,.pdf">
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-info-circle me-1"></i> Format didukung: PNG, JPG, JPEG, GIF, WEBP, BMP, SVG, PDF (Maksimal 10MB)
                                </small>
                            </div>

                            <?php if (!empty($display_gambar_src)): ?>
                                <div class="col-12 mt-3">
                                    <label class="form-label fw-semibold text-dark small d-block">Dokumen / Peta Aktif Saat Ini:</label>
                                    <div class="p-2 border rounded-3 bg-dark d-inline-block" style="max-width: 100%;">
                                        <img src="<?= htmlspecialchars($display_gambar_src) ?>?t=<?= time() ?>" class="img-fluid rounded" style="max-height: 280px;" alt="Dokumen Siaran Pers" onerror="this.src='https://www.bmkg.go.id/asset/img/logo/logo-bmkg.png';">
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="col-12 pt-3">
                                <button type="submit" class="btn btn-warning rounded-pill px-4 py-2 fw-bold text-dark">
                                    <i class="bi bi-cloud-upload-fill me-1.5"></i> Simpan & Terbitkan Siaran Pers
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>