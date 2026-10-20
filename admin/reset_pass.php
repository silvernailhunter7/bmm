<?php
require_once '../koneksi.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(20) DEFAULT 'all'
    )");

    $pdo->exec("DELETE FROM users");

    $pass_hash = password_hash('admin123', PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute(['admin_geo', $pass_hash, 'geo']);
    $stmt->execute(['admin_meteo', $pass_hash, 'meteo']);
    $stmt->execute(['admin_klima', $pass_hash, 'klima']);
    $stmt->execute(['admin', $pass_hash, 'all']);

    echo "<h3 style='color: green;'>Reset Password Berhasil!</h3>";
    echo "<p>Akun berikut telah diaktifkan kembali dengan password: <strong>admin123</strong></p>";
    echo "<ul>
            <li><strong>admin</strong> (Role: Super Admin)</li>
            <li><strong>admin_geo</strong> (Role: Geofisika)</li>
            <li><strong>admin_meteo</strong> (Role: Meteorologi)</li>
            <li><strong>admin_klima</strong> (Role: Klimatologi)</li>
          </ul>";
    echo "<a href='login.php'>Klik disini untuk Login</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>