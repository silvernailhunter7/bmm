<?php
error_reporting(0);
set_time_limit(6);

$img_name = isset($_GET['img']) ? trim($_GET['img']) : '';

// Jika parameter nama file kosong, ambil langsung autogempa untuk mencari nama file Shakemap
if (empty($img_name)) {
    $json_url = "https://data.bmkg.go.id/DataMKG/TEWS/autogempa.json";
    $json_data = @file_get_contents($json_url);
    if ($json_data) {
        $arr = json_decode($json_data, true);
        if (isset($arr['Infogempa']['gempa']['Shakemap'])) {
            $img_name = $arr['Infogempa']['gempa']['Shakemap'];
        }
    }
}

// URL Target Gambar Shakemap
$target_url = "https://data.bmkg.go.id/DataMKG/TEWS/" . $img_name;

function get_remote_image($url) {
    if (!function_exists('curl_init')) {
        return @file_get_contents($url);
    }
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    
    $data = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ($code === 200 && !empty($data)) ? $data : false;
}

$image_binary = false;
if (!empty($img_name)) {
    $image_binary = get_remote_image($target_url);
}

// Render Gambar Asli
if ($image_binary) {
    header('Content-Type: image/jpeg');
    header('Content-Length: ' . strlen($image_binary));
    header('Cache-Control: public, max-age=1800');
    echo $image_binary;
    exit;
} else {
    // Fallback Gambar Langsung dari BMKG
    $fallback_direct = @file_get_contents("https://data.bmkg.go.id/DataMKG/TEWS/autogempa.jpg");
    if ($fallback_direct) {
        header('Content-Type: image/jpeg');
        echo $fallback_direct;
        exit;
    }
    
    // Fallback SVG jika koneksi terputus
    header('Content-Type: image/svg+xml');
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="300" height="380" viewBox="0 0 300 380"><rect width="100%" height="100%" fill="#f1f5f9"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#64748b" font-family="sans-serif" font-size="14">Gagal Memuat Shakemap</text></svg>';
    exit;
}
?>