<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php"); exit;
}

$admin_user = $_SESSION['admin_username'] ?? 'admin';
$admin_role = strtolower($_SESSION['admin_role'] ?? 'all');

if (strpos($admin_role, 'meteo') === false && $admin_role !== 'all') {
    die("<div style='padding:30px;'><h3 style='color:red;'>Akses Ditolak!</h3><a href='index.php'>Kembali</a></div>");
}

$success_msg = ''; $error_msg = '';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS cuaca_penerbangan_pelabuhan (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        kategori VARCHAR(20) NOT NULL,
        judul VARCHAR(255) NOT NULL,
        gambar_path VARCHAR(255) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    $stmt_del = $pdo->prepare("DELETE FROM cuaca_penerbangan_pelabuhan WHERE id = :id AND kategori = 'pelabuhan'");
    $stmt_del->execute([':id' => (int)$_GET['id']]);
    $success_msg = "Gambar cuaca pelabuhan berhasil dihapus!";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_media'])) {
    $judul = !empty($_POST['judul']) ? trim($_POST['judul']) : 'Prakiraan Cuaca Maritim & Pelabuhan Gorontalo';

    if (isset($_FILES['gambar_files']) && !empty($_FILES['gambar_files']['name'][0])) {
        $files = $_FILES['gambar_files'];
        $uploaded = 0;

        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                $target_dir = '../uploads/';
                if (!is_dir($target_dir)) { @mkdir($target_dir, 0777, true); }

                $new_name = 'pelabuhan_' . time() . '_' . rand(1000, 9999) . '.' . ($ext ?: 'png');
                if (move_uploaded_file($files['tmp_name'][$i], $target_dir . $new_name)) {
                    $stmt_ins = $pdo->prepare("INSERT INTO cuaca_penerbangan_pelabuhan (kategori, judul, gambar_path) VALUES ('pelabuhan', :judul, :path)");
                    $stmt_ins->execute([':judul' => $judul, ':path' => 'uploads/' . $new_name]);
                    $uploaded++;
                }
            }
        }
        if ($uploaded > 0) $success_msg = "Berhasil mengunggah $uploaded gambar cuaca pelabuhan!";
        else $error_msg = "Gagal mengunggah file gambar.";
    }
}

$list_data = $pdo->query("SELECT * FROM cuaca_penerbangan_pelabuhan WHERE kategori='pelabuhan' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Cuaca Pelabuhan - Panel Admin</title>
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
        <div class="col-md-3 col-lg-2 sidebar p-3 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center gap-2 mb-4 px-2 pt-2">
                    <img src="https://www.bmkg.go.id/asset/img/logo/logo-bmkg.png" height="36" alt="BMKG">
                    <div><h6 class="fw-bold mb-0 text-white">PANEL ADMIN</h6><small class="text-info"><?= strtoupper($admin_user) ?></small></div>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="pers.php"><i class="bi bi-file-earmark-text me-2"></i> Siaran Pers Meteo</a></li>
                    <li class="nav-item"><a class="nav-link" href="penerbangan.php"><i class="bi bi-airplane me-2"></i> Cuaca Penerbangan</a></li>
                    <li class="nav-item"><a class="nav-link text-info fw-semibold" href="maritim.php"><i class="bi bi-tsunami me-2"></i> Cuaca Pelabuhan</a></li>
            </div>
            <div class="pt-3 border-top border-secondary"><a class="btn btn-outline-danger w-100" href="logout.php"><i class="bi bi-box-arrow-right me-1"></i> Logout</a></div>
        </div>

        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-tsunami text-info me-2"></i>Kelola Cuaca Pelabuhan / Maritim</h5>
                <a href="../pages/cuaca_pelabuhan.php" target="_blank" class="btn btn-outline-info btn-sm rounded-pill px-3"><i class="bi bi-eye me-1"></i> Lihat Tampilan Publik</a>
            </div>

            <?php if ($success_msg): ?><div class="alert alert-success py-2.5"><?= $success_msg ?></div><?php endif; ?>
            <?php if ($error_msg): ?><div class="alert alert-danger py-2.5"><?= $error_msg ?></div><?php endif; ?>

            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-info text-white fw-bold"><i class="bi bi-cloud-upload me-2"></i>Upload Gambar Cuaca Pelabuhan</div>
                <div class="card-body p-4">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Judul / Label Informasi</label>
                                <input type="text" name="judul" class="form-control" placeholder="Contoh: Prakiraan Cuaca Maritim & Pelabuhan Gorontalo" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Pilih Gambar (Bisa pilih lebih dari 1 file sekaligus)</label>
                                <input type="file" name="gambar_files[]" class="form-control" accept="image/*" multiple required>
                            </div>
                            <div class="col-12 pt-2">
                                <button type="submit" name="upload_media" class="btn btn-info rounded-pill text-white px-4"><i class="bi bi-upload me-1"></i> Upload Gambar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border shadow-sm p-3">
                <h6 class="fw-bold text-info mb-3">Daftar Gambar Aktif (<?= count($list_data) ?> Gambar)</h6>
                <div class="row g-3">
                    <?php foreach ($list_data as $item): ?>
                        <div class="col-md-3 position-relative">
                            <img src="../<?= $item['gambar_path'] ?>" class="img-fluid rounded border w-100" style="height: 140px; object-fit: cover;">
                            <a href="?action=delete&id=<?= $item['id'] ?>" onclick="return confirm('Hapus gambar ini?')" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 py-0 px-1.5"><i class="bi bi-x"></i></a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>