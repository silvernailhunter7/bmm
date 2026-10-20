<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);
set_time_limit(5);

$nowcast_url = "https://www.bmkg.go.id/alerts/nowcast/id";

function fetch_nowcast_api($url) {
    if (!function_exists('curl_init')) {
        $ctx = stream_context_create([
            "http" => [
                "method" => "GET",
                "timeout" => 5,
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
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

$raw_xml = fetch_nowcast_api($nowcast_url);

$has_warning = false;
$warning_title = "";
$warning_msg = "Tidak ada peringatan dini cuaca di wilayah Provinsi Gorontalo saat ini. Kondisi terpantau kondusif.";
$expires_time = null;
$expires_formatted = null;

if ($raw_xml) {
    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($raw_xml);

    if ($xml !== false) {
        $items = $xml->channel->item ?? $xml->entry ?? $xml->item ?? [];

        foreach ($items as $item) {
            $title       = (string)($item->title ?? '');
            $description = (string)($item->description ?? $item->summary ?? '');
            $guid        = (string)($item->guid ?? '');

            // Tangkap namespace atau tag penjelas CAP (Common Alerting Protocol) jika ada
            $namespaces = $item->getNameSpaces(true);
            $cap        = isset($namespaces['cap']) ? $item->children($namespaces['cap']) : null;
            
            // Ambil string 'expires' dari namespace CAP atau parsing teks description/item
            $expires_raw = (string)($cap->expires ?? $item->expires ?? '');

            // Deteksi jika data tersebut milik Gorontalo
            if (
                stripos($title, 'Gorontalo') !== false || 
                stripos($description, 'Gorontalo') !== false ||
                stripos($guid, '.75.') !== false
            ) {
                // Jika tag XML tidak memuat tag <expires> khusus, ekstrak via Regex dari teks description/raw
                if (empty($expires_raw)) {
                    if (preg_match('/Expires\s*:\s*([0-9T\:\+\-\s]+)/i', $description, $exp_match)) {
                        $expires_raw = trim($exp_match[1]);
                    } elseif (preg_match('/berlangsung hingga\s*([0-9A-Za-z\:\s]+WITA)/i', $description, $exp_match2)) {
                        $expires_raw = trim($exp_match2[1]);
                    }
                }

                // VALIDASI WAKTU EXPIRED
                $is_expired = false;
                if (!empty($expires_raw)) {
                    $exp_timestamp = strtotime($expires_raw);
                    if ($exp_timestamp !== false) {
                        $expires_time = $exp_timestamp;
                        $expires_formatted = date('d M Y H:i', $exp_timestamp) . ' WITA';
                        
                        // Cek apakah waktu kedaluwarsa sudah terlewati dibanding waktu sekarang
                        if (time() > $exp_timestamp) {
                            $is_expired = true;
                        }
                    }
                }

                // Hanya set warning aktif jika BELUM kedaluwarsa
                if (!$is_expired) {
                    $has_warning   = true;
                    $warning_title = trim(strip_tags($title));
                    $clean_desc    = trim(strip_tags($description));

                    // Bersihkan frasa header CAP berulang jika ada
                    $clean_desc = preg_replace('/Language\s*:\s*id|Event\s*:.*?|Urgency\s*:.*?|Severity\s*:.*?|Certainty\s*:.*?|Effective\s*:.*?|Expires\s*:.*?|Sender Name\s*:.*?|Headline\s*:.*/is', '', $clean_desc);
                    $clean_desc = trim(preg_replace('/\s+/', ' ', $clean_desc));

                    if (!empty($clean_desc)) {
                        $warning_msg = $clean_desc;
                    } else {
                        $warning_msg = $warning_title;
                    }

                    // Tambahkan info waktu berlaku sampai jam berapa jika tersedia
                    if ($expires_formatted) {
                        $warning_msg .= " (Berlaku hingga: " . $expires_formatted . ")";
                    }
                }

                break; // Stop loop setelah memproses data Gorontalo
            }
        }
    }
}

echo json_encode([
    'active'    => $has_warning,
    'title'     => $warning_title,
    'message'   => $warning_msg,
    'expires'   => $expires_formatted,
    'source'    => 'BMKG Nowcast RSS/XML CAP Feed',
    'timestamp' => date('d M Y H:i:s') . ' WITA'
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;