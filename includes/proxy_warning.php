<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);
set_time_limit(5);

$source_url = "https://www.bmkg.go.id/cuaca/peringatan-dini-cuaca";

function fetch_nowcast_bmkg($url) {
    if (!function_exists('curl_init')) {
        $ctx = stream_context_create([
            "http" => [
                "method" => "GET",
                "timeout" => 4,
                "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n"
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
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

$html = fetch_nowcast_bmkg($source_url);

$has_warning = false;
$warning_msg = "Tidak ada peringatan dini cuaca di wilayah Provinsi Gorontalo saat ini. Kondisi terpantau kondusif.";
$details = [];

if ($html) {
    // Cari kemunculan kata "Gorontalo" pada daftar peringatan aktif
    if (stripos($html, 'Gorontalo') !== false) {
        // Ekstrak blok konten peringatan jika Gorontalo ada dalam daftar
        $has_warning = true;
        
        // Coba ambil potongan teks narasi jika ditemukan
        preg_match_all('/Gorontalo[^\<]+/i', $html, $matches);
        if (!empty($matches[0])) {
            $cleaned = array_map('strip_tags', $matches[0]);
            $warning_msg = "PERINGATAN DINI GORONTALO: " . implode(" - ", array_slice($cleaned, 0, 2));
        } else {
            $warning_msg = "Potensi Hujan Sedang-Lebat disertai Kilat/Petir dan Angin Kencang di wilayah Provinsi Gorontalo.";
        }
    }
}

echo json_encode([
    'active'    => $has_warning,
    'message'   => $warning_msg,
    'source'    => 'BMKG Nowcasting Indonesia',
    'timestamp' => date('d M Y H:i:s') . ' WITA'
]);
exit;