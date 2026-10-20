<?php
// Set header sebagai gambar GIF
header('Content-Type: image/gif');
header('Cache-Control: no-cache, no-store, must-revalidate');

$url = "http://202.90.198.22/IMAGE/ANIMASI/H08_EH_Gorontalo_m18.gif";

// Ambil GIF dari server IP BMKG menggunakan cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

$gif_data = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code === 200 && $gif_data) {
    echo $gif_data;
} else {
    // Fallback jika IP server BMKG sedang RTO/Maintenance
    $fallback_url = "https://data.bmkg.go.id/DataMKG/SATELIT/H08_EH_Indonesia.png";
    readfile($fallback_url);
}
?>