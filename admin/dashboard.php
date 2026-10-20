<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}

$username_session = $_SESSION['username'] ?? '';
$role_user         = $_SESSION['role'] ?? '';
$is_admin_klima    = ($role_user === 'admin_klimatologi' || $role_user === 'superadmin');

$success = '';
$error   = '';

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

// Handle Ubah Password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $pass_lama = trim($_POST['pass_lama']);
    $pass_baru = trim($_POST['pass_baru']);
    $konf_pass  = trim($_POST['konf_pass']);

    if (empty($pass_lama) || empty($pass_baru) || empty($konf_pass)) {
        $error = 'Semua bidang password wajib diisi!';
    } elseif ($pass_baru !== $konf_pass) {
        $error = 'Konfirmasi password baru tidak cocok!';
    } else {
        // Fetch current user from database
        $user_db = null;
        if (isset($pdo)) {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username_session]);
            $user_db = $stmt->fetch();
        } else {
            $u_esc = mysqli_real_escape_string($koneksi, $username_session);
            $q = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$u_esc'");
            $user_db = $q ? mysqli_fetch_assoc($q) : null;
        }

        if ($user_db && (password_verify($pass_lama, $user_db['password']) || $pass_lama === $user_db['password'])) {
            $new_hash = password_hash($pass_baru, PASSWORD_BCRYPT);
            
            if (isset($pdo)) {
                $up_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
                $up_stmt->execute([$new_hash, $username_session]);
            } else {
                $hash_esc = mysqli_real_escape_string($koneksi, $new_hash);
                $u_esc    = mysqli_real_escape_string($koneksi, $username_session);
                mysqli_query($koneksi, "UPDATE users SET password = '$hash_esc' WHERE username = '$u_esc'");
            }
            $success = 'Password berhasil diperbarui!';
        } else {
            $error = 'Password lama Anda salah!';
        }
    }
}

// Handle Hapus Berita
if (isset($_GET['delete'])) {
    $id_del = (int)$_GET['delete'];
    if (isset($pdo)) {
        $stmt = $pdo->prepare("DELETE FROM berita WHERE id = ?");
        $stmt->execute([$id_del]);
    } else {
        mysqli_query($koneksi, "DELETE FROM berita WHERE id = $id_del");
    }
    header("Location: dashboard.php?msg=deleted");
    exit;
}

// Handle Update Iklim Semester
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_iklim'])) {
    if (!$is_admin_klima) {
        $error = 'Akses ditolak! Hanya Admin Klimatologi yang diizinkan.';
    } else {
        $judul_iklim  = trim($_POST['judul_iklim']);
        $desk_iklim   = trim($_POST['desk_iklim']);
        $gambar_iklim = 'https://data.bmkg.go.id/datamkg/MEWS/Klimatologi/Prakiraan_Hujan_Bulanan.png';

        if (isset($_FILES['gambar_iklim_file']) && $_FILES['gambar_iklim_file']['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES['gambar_iklim_file']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $new_name = 'iklim_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['gambar_iklim_file']['tmp_name'], '../uploads/' . $new_name)) {
                    $gambar_iklim = 'uploads/' . $new_name;
                }
            }
        } elseif (!empty($_POST['gambar_iklim_url'])) {
            $gambar_iklim = trim($_POST['gambar_iklim_url']);
        }

        if (isset($pdo)) {
            $pdo->exec("CREATE TABLE IF NOT EXISTS iklim (id INTEGER PRIMARY KEY AUTOINCREMENT, judul TEXT, deskripsi TEXT, gambar TEXT, created_at DATETIME DEFAULT CURRENT_TIMESTAMP)");
            $stmt = $pdo->prepare("REPLACE INTO iklim (id, judul, deskripsi, gambar) VALUES (1, ?, ?, ?)");
            $stmt->execute([$judul_iklim, $desk_iklim, $gambar_iklim]);
        } else {
            mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS iklim (id INT AUTO_INCREMENT PRIMARY KEY, judul VARCHAR(255) NOT NULL, deskripsi TEXT NOT NULL, gambar VARCHAR(255) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
            $j = mysqli_real_escape_string($koneksi, $judul_iklim); $d = mysqli_real_escape_string($koneksi, $desk_iklim); $g = mysqli_real_escape_string($koneksi, $gambar_iklim);
            mysqli_query($koneksi, "REPLACE INTO iklim (id, judul, deskripsi, gambar) VALUES (1, '$j', '$d', '$g')");
        }
        $success = 'Data Analisis Iklim berhasil diperbarui!';
    }
}

// Handle Update Cuaca 10 Harian
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_dasarian'])) {
    if (!$is_admin_klima) {
        $error = 'Akses ditolak! Hanya Admin Klimatologi yang diizinkan.';
    } else {
        $judul_das  = trim($_POST['judul_dasarian']);
        $desk_das   = trim($_POST['desk_dasarian']);
        $gambar_das = 'https://data.bmkg.go.id/datamkg/MEWS/Klimatologi/Prakiraan_Hujan_Dasarian.png';

        if (isset($_FILES['gambar_das_file']) && $_FILES['gambar_das_file']['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES['gambar_das_file']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $new_name = 'dasarian_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['gambar_das_file']['tmp_name'], '../uploads/' . $new_name)) {
                    $gambar_das = 'uploads/' . $new_name;
                }
            }
        } elseif (!empty($_POST['gambar_das_url'])) {
            $gambar_das = trim($_POST['gambar_das_url']);
        }

        if (isset($pdo)) {
            $pdo->exec("CREATE TABLE IF NOT EXISTS cuaca_10harian (id INTEGER PRIMARY KEY AUTOINCREMENT, judul TEXT, deskripsi TEXT, gambar TEXT, created_at DATETIME DEFAULT CURRENT_TIMESTAMP)");
            $stmt = $pdo->prepare("REPLACE INTO cuaca_10harian (id, judul, deskripsi, gambar) VALUES (1, ?, ?, ?)");
            $stmt->execute([$judul_das, $desk_das, $gambar_das]);
        } else {
            mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS cuaca_10harian (id INT AUTO_INCREMENT PRIMARY KEY, judul VARCHAR(255) NOT NULL, deskripsi TEXT NOT NULL, gambar VARCHAR(255) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
            $j = mysqli_real_escape_string($koneksi, $judul_das); $d = mysqli_real_escape_string($koneksi, $desk_das); $g = mysqli_real_escape_string($koneksi, $gambar_das);
            mysqli_query($koneksi, "REPLACE INTO cuaca_10harian (id, judul, deskripsi, gambar) VALUES (1, '$j', '$d', '$g')");
        }
        $success = 'Data Cuaca Per 10 Harian berhasil diperbarui!';
    }
}

// Handle Tambah Berita
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_berita'])) {
    $judul    = trim($_POST['judul']);
    $kategori = trim($_POST['kategori']);
    $isi      = trim($_POST['isi']);
    $penulis  = $_SESSION['nama'] ?? 'Admin BMKG';
    $gambar   = 'https://images.unsplash.com/photo-1516834474-48c0abc2a902?w=800';

    if (isset($_FILES['gambar_file']) && $_FILES['gambar_file']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['gambar_file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $new_name = 'news_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['gambar_file']['tmp_name'], '../uploads/' . $new_name)) {
                $gambar = 'uploads/' . $new_name;
            }
        }
    } elseif (!empty($_POST['gambar_url'])) {
        $gambar = trim($_POST['gambar_url']);
    }

    if (isset($pdo)) {
        $stmt = $pdo->prepare("INSERT INTO berita (judul, kategori, isi, gambar, penulis) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$judul, $kategori, $isi, $gambar, $penulis]);
    } else {
        $j = mysqli_real_escape_string($koneksi, $judul); $k = mysqli_real_escape_string($koneksi, $kategori); $i = mysqli_real_escape_string($koneksi, $isi); $g = mysqli_real_escape_string($koneksi, $gambar); $p = mysqli_real_escape_string($koneksi, $penulis);
        mysqli_query($koneksi, "INSERT INTO berita (judul, kategori, isi, gambar, penulis) VALUES ('$j', '$k', '$i', '$g', '$p')");
    }
    $success = 'Berita berhasil diterbitkan!';
}

// Fetch Data
if (isset($pdo)) {
    $berita_list  = $pdo->query("SELECT * FROM berita ORDER BY created_at DESC")->fetchAll();
    $curr_iklim   = $pdo->query("SELECT * FROM iklim WHERE id = 1")->fetch();
    $curr_dasarian= $pdo->query("SELECT * FROM cuaca_10harian WHERE id = 1")->fetch();
} else {
    $q = mysqli_query($koneksi, "SELECT * FROM berita ORDER BY created_at DESC"); $berita_list = $q ? mysqli_fetch_all($q, MYSQLI_ASSOC) : [];
    $q2 = mysqli_query($koneksi, "SELECT * FROM iklim WHERE id = 1"); $curr_iklim = $q2 ? mysqli_fetch_assoc($q2) : null;
    $q3 = mysqli_query($koneksi, "SELECT * FROM cuaca_10harian WHERE id = 1"); $curr_dasarian = $q3 ? mysqli_fetch_assoc($q3) : null;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - BMKG Gorontalo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .sidebar { background-color: #0f172a; color: white; min-height: 100vh; }
        .card-modern { border: none; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3 col-lg-2 sidebar p-3 d-flex flex-column">
            <div class="d-flex align-items-center gap-2 mb-4 px-2">
                <img src="https://www.bmkg.go.id/asset/img/logo/logo-bmkg.png" height="35" alt="BMKG">
                <span class="fw-bold fs-6">ADMIN BMKG</span>
            </div>
            
            <div class="mb-3 px-2">
                <small class="text-secondary d-block">Pengguna Aktif:</small>
                <strong class="text-warning"><?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?></strong>
                <span class="badge bg-primary text-uppercase d-block mt-1"><?= htmlspecialchars($role_user) ?></span>
            </div>

            <hr class="border-secondary">

            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link active rounded-3 mb-1"><i class="bi bi-speedometer2 me-2"></i> Dashboard Utama</a>
                </li>
                <li class="nav-item">
                    <a href="../index.php" target="_blank" class="nav-link text-white rounded-3 mb-1"><i class="bi bi-globe me-2"></i> Lihat Website</a>
                </li>
            </ul>

            <a href="dashboard.php?action=logout" class="btn btn-outline-danger w-100 mt-auto rounded-pill btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </a>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0 text-slate-800">Dashboard Panel Admin BMKG Gorontalo</h4>
                <a href="../index.php" class="btn btn-sm btn-outline-primary rounded-pill">« Ke Beranda</a>
            </div>

            <?php if ($success || isset($_GET['msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i> <?= $success ?: 'Data berhasil diperbarui.' ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- FORM UBAH PASSWORD ADMIN -->
            <div class="card card-modern p-4 mb-4 border-start border-4 border-warning">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-shield-lock text-warning me-2"></i>Ubah Password Akun Admin</h5>
                <form action="" method="POST">
                    <input type="hidden" name="change_password" value="1">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Password Lama</label>
                            <input type="password" name="pass_lama" class="form-control" placeholder="••••••••" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Password Baru</label>
                            <input type="password" name="pass_baru" class="form-control" placeholder="••••••••" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Konfirmasi Password Baru</label>
                            <input type="password" name="konf_pass" class="form-control" placeholder="••••••••" required>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 fw-semibold btn-sm">
                                <i class="bi bi-key me-1"></i> Perbarui Password
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- FORM KELOLA KLIMATOLOGI (KHUSUS ADMIN KLIMATOLOGI / SUPERADMIN) -->
            <?php if ($is_admin_klima): ?>
                <div class="card card-modern p-4 mb-4 border-start border-4 border-info">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-bar-chart-line text-info me-2"></i>Kelola Analisis Iklim Per Semester</h5>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="update_iklim" value="1">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-semibold">Judul Laporan Iklim</label>
                                <input type="text" name="judul_iklim" class="form-control" value="<?= htmlspecialchars($curr_iklim['judul'] ?? 'Analisis & Prakiraan Musim Hujan Semester II - 2026') ?>" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-semibold">Ringkasan Analisis Iklim</label>
                                <textarea name="desk_iklim" class="form-control" rows="2" required><?= htmlspecialchars($curr_iklim['deskripsi'] ?? 'Semester II - 2026: Puncak musim hujan diperkirakan terjadi pada bulan November - Desember dengan curah hujan rata-rata berkisar antara 200 - 350 mm/bulan.') ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Upload Peta/Grafik Iklim</label>
                                <input type="file" name="gambar_iklim_file" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Atau Link URL Gambar BMKG</label>
                                <input type="url" name="gambar_iklim_url" class="form-control" value="<?= htmlspecialchars($curr_iklim['gambar'] ?? 'https://data.bmkg.go.id/datamkg/MEWS/Klimatologi/Prakiraan_Hujan_Bulanan.png') ?>">
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-info text-white rounded-pill px-4 fw-semibold btn-sm">
                                    <i class="bi bi-save me-1"></i> Simpan Laporan Iklim
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card card-modern p-4 mb-4 border-start border-4 border-primary">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-calendar-week text-primary me-2"></i>Kelola Cuaca Per 10 Harian / Dasarian</h5>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="update_dasarian" value="1">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label small fw-semibold">Judul Dasarian</label>
                                <input type="text" name="judul_dasarian" class="form-control" value="<?= htmlspecialchars($curr_dasarian['judul'] ?? 'Prakiraan Hujan Dasarian (10 Harian) Gorontalo') ?>" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-semibold">Deskripsi Dasarian</label>
                                <textarea name="desk_dasarian" class="form-control" rows="2" required><?= htmlspecialchars($curr_dasarian['deskripsi'] ?? 'Informasi akumulasi curah hujan dan sifat hujan per 10 hari untuk bidang pertanian dan keilmuan klimatologi.') ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Upload Peta Dasarian</label>
                                <input type="file" name="gambar_das_file" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Atau Link URL Gambar BMKG</label>
                                <input type="url" name="gambar_das_url" class="form-control" value="<?= htmlspecialchars($curr_dasarian['gambar'] ?? 'https://data.bmkg.go.id/datamkg/MEWS/Klimatologi/Prakiraan_Hujan_Dasarian.png') ?>">
                            </div>
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold btn-sm">
                                    <i class="bi bi-save me-1"></i> Simpan Data Dasarian
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <!-- FORM TAMBAH BERITA -->
            <div class="card card-modern p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-plus-circle text-primary me-2"></i>Tambah Berita Baru</h5>
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="tambah_berita" value="1">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold">Judul Berita</label>
                            <input type="text" name="judul" class="form-control" placeholder="Judul berita cuaca/gempa..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Kategori Divisi</label>
                            <select name="kategori" class="form-select" required>
                                <option value="Meteorologi">Meteorologi</option>
                                <option value="Klimatologi">Klimatologi</option>
                                <option value="Geofisika">Geofisika</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Upload File Gambar</label>
                            <input type="file" name="gambar_file" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Atau Link URL Gambar</label>
                            <input type="url" name="gambar_url" class="form-control" placeholder="https://example.com/image.jpg">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Isi Berita</label>
                            <textarea name="isi" class="form-control" rows="3" placeholder="Isi berita..." required></textarea>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold btn-sm">
                                <i class="bi bi-send me-1"></i> Terbitkan Berita
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- TABEL DAFTAR BERITA -->
            <div class="card card-modern p-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-list-stars me-2 text-primary"></i>Daftar Berita Diterbitkan</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Gambar</th>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Penulis</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($berita_list)): ?>
                                <?php foreach ($berita_list as $b): ?>
                                    <tr>
                                        <td><img src="../<?= htmlspecialchars($b['gambar']) ?>" style="width: 50px; height: 35px; object-fit: cover;" class="rounded" onerror="this.src='<?= htmlspecialchars($b['gambar']) ?>'"></td>
                                        <td class="fw-semibold text-dark"><?= htmlspecialchars($b['judul']) ?></td>
                                        <td><span class="badge bg-info-subtle text-info-emphasis px-2 py-1"><?= htmlspecialchars($b['kategori']) ?></span></td>
                                        <td><small><?= htmlspecialchars($b['penulis']) ?></small></td>
                                        <td>
                                            <a href="dashboard.php?delete=<?= $b['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus berita ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>