<?php
error_reporting(0);
set_time_limit(8);

// Endpoint Resmi Radar CMAX BMKG Gorontalo
$primary_url = "https://cuaca.bmkg.go.id/data/public/ANIMASI/CMAX_Gorontalo.gif";

function fetch_radar_cmax($url, $timeout = 5) {
    if (!function_exists('curl_init')) {
        $ctx = stream_context_create([
            "http" => [
                "timeout" => $timeout,
                "header"  => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\nReferer: https://cuaca.bmkg.go.id/\r\n"
            ],
            "ssl" => ["verify_peer" => false, "verify_peer_name" => false]
        ]);
        return @file_get_contents($url, false, $ctx);
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_REFERER, 'http://cuaca.bmkg.go.id/');

    $data = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ($http_code === 200 && !empty($data)) ? $data : false;
}

// Unduh Gambar Animasi Radar CMAX Gorontalo
$img_data = fetch_radar_cmax($primary_url, 5);

if ($img_data) {
    header("Content-Type: image/gif");
    header("Cache-Control: no-cache, must-revalidate");
    header("Content-Length: " . strlen($img_data));
    echo $img_data;
    exit;
} else {
    // Fallback Canvas jika server Sidarma sedang RTO/Maintenance
    header('Content-Type: image/svg+xml');
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="800" height="400" viewBox="0 0 800 400"><rect width="100%" height="100%" fill="#0f172a"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#38bdf8" font-family="sans-serif" font-size="16" font-weight="bold">Server Radar CMAX Gorontalo Sedang Pemeliharaan</text></svg>';
    exit;
}
?>