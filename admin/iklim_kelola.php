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

// 2. PENGECEKAN RBAC FLEKSIBEL (Meloloskan role klimatologi & super admin)
if (strpos($admin_role, 'klim') === false && $admin_role !== 'all') {
    die("<div style='padding:30px; font-family:sans-serif;'>
            <h3 style='color:red;'>Akses Ditolak!</h3>
            <p>Halaman ini hanya dapat diakses oleh <strong>Admin Klimatologi</strong>.</p>
            <a href='index.php'>Kembali ke Dashboard Admin</a>
         </div>");
}

$success_msg = '';
$error_msg   = '';

// 3. Buat / Merekondisi Tabel iklim_semester
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS iklim_semester (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        semester VARCHAR(50) NOT NULL DEFAULT 'Semester II',
        tahun VARCHAR(10) NOT NULL DEFAULT '2026',
        deskripsi TEXT NOT NULL,
        gambar_peta VARCHAR(255) NOT NULL,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    // Abaikan jika tabel sudah ada
}

// 4. AMBIL DATA IKLIM TERBARU UNTUK DEFAULT VALUE
$data_iklim = null;
try {
    $stmt_get = $pdo->query("SELECT * FROM iklim_semester ORDER BY id DESC LIMIT 1");
    $data_iklim = $stmt_get ? $stmt_get->fetch(PDO::FETCH_ASSOC) : null;
} catch (PDOException $e) {
    $data_iklim = null;
}

$def_semester  = !empty($data_iklim['semester']) ? $data_iklim['semester'] : 'Semester II';
$def_tahun     = !empty($data_iklim['tahun']) ? $data_iklim['tahun'] : date('Y');
$def_deskripsi = !empty($data_iklim['deskripsi']) ? $data_iklim['deskripsi'] : 'Puncak musim hujan diperkirakan terjadi pada bulan November - Desember dengan curah hujan rata-rata berkisar antara 200 - 350 mm/bulan.';
$def_gambar    = !empty($data_iklim['gambar_peta']) ? $data_iklim['gambar_peta'] : 'assets/img/default-iklim.jpg';

// 5. PROSES SIMPAN / UPDATE DATA (HANDLING POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Cek apakah POST kosong akibat ukuran file melebihi post_max_size PHP
    if (empty($_POST) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
        $error_msg = "Ukuran file gambar yang diunggah melebihi batas maksimum server (post_max_size). Silakan kompres gambar atau pilih gambar berukuran lebih kecil (di bawah 5MB).";
    } else {
        // AMBIL INPUT DENGAN GARANSI TIDAK PERNAH NULL
        $semester  = (!empty($_POST['semester'])) ? trim($_POST['semester']) : $def_semester;
        $tahun     = (!empty($_POST['tahun'])) ? trim($_POST['tahun']) : $def_tahun;
        $deskripsi = (!empty($_POST['deskripsi'])) ? trim($_POST['deskripsi']) : $def_deskripsi;
        
        $gambar_path = $def_gambar; // Default gambar lama

        // HANDLE UPLOAD GAMBAR PETA
        if (isset($_FILES['gambar_peta']) && $_FILES['gambar_peta']['error'] === UPLOAD_ERR_OK) {
            $file_tmp  = $_FILES['gambar_peta']['tmp_name'];
            $file_name = $_FILES['gambar_peta']['name'];
            $ext       = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $target_dir = '../uploads/';
            if (!is_dir($target_dir)) {
                @mkdir($target_dir, 0777, true);
            }

            $new_name    = 'iklim_peta_' . time() . '_' . rand(1000, 9999) . '.' . ($ext ?: 'png');
            $target_file = $target_dir . $new_name;

            if (move_uploaded_file($file_tmp, $target_file)) {
                $gambar_path = 'uploads/' . $new_name;
            } else {
                $error_msg = "Gagal memindahkan file terunggah ke folder server.";
            }
        } elseif (isset($_FILES['gambar_peta']) && $_FILES['gambar_peta']['error'] === UPLOAD_ERR_INI_SIZE) {
            $error_msg = "File gambar terlalu besar untuk batas upload PHP server. Silakan gunakan file gambar berukuran lebih kecil.";
        } elseif (isset($_FILES['gambar_peta']) && $_FILES['gambar_peta']['error'] !== UPLOAD_ERR_NO_FILE) {
            $error_msg = "Gagal mengunggah file. Kode Error PHP: " . $_FILES['gambar_peta']['error'];
        }

        // PROSES INSERT KE DATABASE DENGAN JAMINAN TANPA ERROR NULL
        if (empty($error_msg)) {
            try {
                $stmt_inst = $pdo->prepare("INSERT INTO iklim_semester (semester, tahun, deskripsi, gambar_peta, updated_at) VALUES (:semester, :tahun, :deskripsi, :gambar, :updated_at)");
                $stmt_inst->execute([
                    ':semester'   => (string)$semester,
                    ':tahun'      => (string)$tahun,
                    ':deskripsi'  => (string)$deskripsi,
                    ':gambar'     => (string)$gambar_path,
                    ':updated_at' => date('Y-m-d H:i:s')
                ]);
                
                $success_msg = "Data Analisis & Gambar Peta Iklim Semesteran berhasil diperbarui!";
                
                // Refresh variabel display
                $def_semester  = $semester;
                $def_tahun     = $tahun;
                $def_deskripsi = $deskripsi;
                $def_gambar    = $gambar_path;
            } catch (PDOException $e) {
                $error_msg = "Gagal menyimpan ke database: " . $e->getMessage();
            }
        }
    }
}

$display_gambar_src = (strpos($def_gambar, 'http') === 0) ? $def_gambar : '../' . $def_gambar;
$page_title = "Kelola Analisis Iklim Semesteran - Panel Admin";
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
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="berita_kelola.php"><i class="bi bi-newspaper me-2"></i> Kelola Berita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="iklim_kelola.php"><i class="bi bi-calendar3 me-2"></i> Analisis Iklim Semester</a>
                    </li>
                </ul>
            </div>

            <div class="pt-3 border-top border-secondary">
                <a class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2" href="logout.php">
                    <i class="bi bi-box-arrow-right"></i> Keluar / Logout
                </a>
            </div>
        </div>

        <!-- MAIN CONTENT AREA -->
        <div class="col-md-9 col-lg-10 p-4">
            
            <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded-3 shadow-sm border">
                <div>
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-bar-chart-line text-success me-2"></i>Kelola Analisis & Prakiraan Musim Hujan Per-Semester</h5>
                    <small class="text-muted">Fitur khusus Admin Klimatologi Gorontalo</small>
                </div>
                <a href="../pages/iklim_semester.php" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
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

            <!-- FORM UPDATE IKLIM SEMESTER -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-success text-white py-3 px-4">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Form Update Analisis Musim & Peta Iklim</h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="row g-3">
                            
                            <div class="col-md-6">
                                <label for="semester" class="form-label fw-semibold text-dark small">Periode Semester</label>
                                <select name="semester" id="semester" class="form-select" required>
                                    <option value="Semester I" <?= ($def_semester === 'Semester I') ? 'selected' : '' ?>>Semester I (Januari - Juni)</option>
                                    <option value="Semester II" <?= ($def_semester === 'Semester II') ? 'selected' : '' ?>>Semester II (Juli - Desember)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="tahun" class="form-label fw-semibold text-dark small">Tahun</label>
                                <input type="number" name="tahun" id="tahun" class="form-control" value="<?= htmlspecialchars($def_tahun) ?>" required>
                            </div>

                            <div class="col-12">
                                <label for="deskripsi" class="form-label fw-semibold text-dark small">Deskripsi / Prakiraan Ringkas</label>
                                <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control" placeholder="Tuliskan narasi ringkas puncak musim hujan atau estimasi curah hujan..." required><?= htmlspecialchars($def_deskripsi) ?></textarea>
                            </div>

                            <div class="col-12">
                                <label for="gambar_peta" class="form-label fw-semibold text-dark small">Upload Gambar Peta Analisis Curah Hujan & Iklim BMKG</label>
                                <input type="file" name="gambar_peta" id="gambar_peta" class="form-control" accept="image/*,.pdf">
                                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-info-circle me-1"></i> Disarankan file gambar (PNG, JPG, WEBP) berukuran di bawah 5MB.
                                </small>
                            </div>

                            <?php if (!empty($display_gambar_src)): ?>
                                <div class="col-12 mt-3">
                                    <label class="form-label fw-semibold text-dark small d-block">Peta Aktif Saat Ini:</label>
                                    <div class="p-2 border rounded-3 bg-dark d-inline-block" style="max-width: 100%;">
                                        <img src="<?= htmlspecialchars($display_gambar_src) ?>?t=<?= time() ?>" class="img-fluid rounded" style="max-height: 250px;" alt="Peta Aktif" onerror="this.src='https://www.bmkg.go.id/asset/img/logo/logo-bmkg.png';">
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="col-12 pt-3">
                                <button type="submit" class="btn btn-success rounded-pill px-4 py-2 fw-semibold">
                                    <i class="bi bi-cloud-upload-fill me-1.5"></i> Simpan & Terbitkan Analisis
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