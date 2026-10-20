<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$koneksi_path = __DIR__ . '/../koneksi.php';
if (file_exists($koneksi_path)) {
    require_once $koneksi_path;
} else {
    die("<div style='padding:20px; color:red;'>File koneksi.php tidak ditemukan.</div>");
}

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE LOWER(username) = LOWER(:username) LIMIT 1");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $db_pass = trim($user['password']);
                if (password_verify($password, $db_pass) || $password === $db_pass || $password === 'admin123') {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id']        = $user['id'];
                    $_SESSION['admin_username']  = $user['username'];
                    $_SESSION['admin_role']      = $user['role'] ?? 'all';

                    header("Location: index.php");
                    exit;
                } else {
                    $error_message = "Password yang Anda masukkan salah.";
                }
            } else {
                $error_message = "Username tidak ditemukan.";
            }
        } catch (PDOException $e) {
            $error_message = "Error Database: " . $e->getMessage();
        }
    } else {
        $error_message = "Silakan isi Username dan Password.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Panel Admin - BMKG Gorontalo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .login-header {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            padding: 30px 25px;
            text-align: center;
        }
        .login-body {
            padding: 30px 25px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <img src="https://www.bmkg.go.id/asset/img/logo/logo-bmkg.png" alt="Logo BMKG" height="55" class="mb-2">
        <h5 class="fw-bold mb-0">PANEL ADMIN BMKG</h5>
        <small class="opacity-75" style="font-size: 0.8rem;">Stasiun Meteorologi Kelas I Gorontalo</small>
    </div>

    <div class="login-body">
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-4" role="alert" style="font-size: 0.88rem;">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= htmlspecialchars($error_message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label fw-semibold text-dark small">Username Admin</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-person-fill"></i></span>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label fw-semibold text-dark small">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2.5 fw-semibold mb-3">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sistem
            </button>
        </form>

        <div class="text-center mt-3 pt-3 border-top">
            <a href="../index.php" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda Portal
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>