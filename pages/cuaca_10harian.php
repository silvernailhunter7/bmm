<?php 
require_once '../koneksi.php'; 
$page_title = "Cuaca Per 10 Harian (Dasarian) - BMKG Gorontalo";
$is_subpage = true;

// Ambil data dasarian dari database
$data_dasarian = null;
try {
    if (isset($pdo)) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS cuaca_10harian (id INTEGER PRIMARY KEY AUTOINCREMENT, judul TEXT, deskripsi TEXT, gambar TEXT, created_at DATETIME DEFAULT CURRENT_TIMESTAMP)");
        $data_dasarian = $pdo->query("SELECT * FROM cuaca_10harian ORDER BY id DESC LIMIT 1")->fetch();
    } else {
        mysqli_query($koneksi, "CREATE TABLE IF NOT EXISTS cuaca_10harian (id INT AUTO_INCREMENT PRIMARY KEY, judul VARCHAR(255) NOT NULL, deskripsi TEXT NOT NULL, gambar VARCHAR(255) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
        $q = mysqli_query($koneksi, "SELECT * FROM cuaca_10harian ORDER BY id DESC LIMIT 1");
        $data_dasarian = $q ? mysqli_fetch_assoc($q) : null;
    }
} catch (Exception $e) { $data_dasarian = null; }

$judul = $data_dasarian['judul'] ?? 'Prakiraan Hujan Dasarian (10 Harian) Gorontalo';
$deskripsi = $data_dasarian['deskripsi'] ?? 'Informasi akumulasi curah hujan dan sifat hujan per 10 hari untuk bidang pertanian dan keilmuan klimatologi.';
$gambar = $data_dasarian['gambar'] ?? 'https://data.bmkg.go.id/datamkg/MEWS/Klimatologi/Prakiraan_Hujan_Dasarian.png';

include '../includes/header.php';
?>

<div class="container px-lg-5 my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none">Beranda</a></li>
            <li class="breadcrumb-item">Klimatologi</li>
            <li class="breadcrumb-item active">Cuaca Per 10 Harian</li>
        </ol>
    </nav>

    <div class="card card-modern p-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h4 class="fw-bold mb-0 text-slate-800"><i class="bi bi-calendar-week text-primary me-2"></i><?= htmlspecialchars($judul) ?></h4>
            <span class="badge bg-primary rounded-pill px-3 py-2">Update Dasarian BMKG</span>
        </div>
        <p class="text-muted small mb-4"><?= htmlspecialchars($deskripsi) ?></p>

        <!-- Peta / Gambar Prakiraan Dasarian -->
        <div class="card border-0 shadow-sm overflow-hidden mb-4 bg-dark text-center p-3 rounded-4">
            <div class="mb-2 text-white-50 small text-start"><i class="bi bi-image me-1"></i> Peta Akumulasi Curah Hujan Dasarian BMKG:</div>
            <img src="<?= htmlspecialchars($gambar) ?>" class="img-fluid rounded-3 mx-auto" alt="Peta Dasarian BMKG" style="max-height: 520px; object-fit: contain;" onerror="this.src='https://data.bmkg.go.id/datamkg/MEWS/Klimatologi/Prakiraan_Hujan_Dasarian.png'">
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-3 mb-4">
            <div class="col">
                <div class="p-3 border rounded-4 bg-light">
                    <span class="badge bg-primary mb-2">Dasarian I (Tgl 1 - 10)</span>
                    <h6 class="fw-bold text-dark mb-1">150 - 200 mm</h6>
                    <small class="text-muted d-block">Kategori Curah Hujan: Menengah - Tinggi. Sifat Hujan Atas Normal (AN).</small>
                </div>
            </div>
            <div class="col">
                <div class="p-3 border rounded-4 bg-light">
                    <span class="badge bg-primary mb-2">Dasarian II (Tgl 11 - 20)</span>
                    <h6 class="fw-bold text-dark mb-1">100 - 150 mm</h6>
                    <small class="text-muted d-block">Kategori Curah Hujan: Menengah. Sifat Hujan Normal (N).</small>
                </div>
            </div>
            <div class="col">
                <div class="p-3 border rounded-4 bg-light">
                    <span class="badge bg-primary mb-2">Dasarian III (Tgl 21 - 30/31)</span>
                    <h6 class="fw-bold text-dark mb-1">200 - 300 mm</h6>
                    <small class="text-muted d-block">Kategori Curah Hujan: Tinggi. Potensi genangan air di beberapa kecamatan.</small>
                </div>
            </div>
        </div>

        <a href="../index.php" class="btn btn-outline-secondary rounded-pill btn-sm align-self-start">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>