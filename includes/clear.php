<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/api_gempa.php';

// FUNGSI CACHING & PARSING DATA CUACA UTAMA
function get_cuaca_adm4_smart_cache($adm4) {
    $cache_dir = __DIR__ . '/../cache/';
    if (!is_dir($cache_dir)) {
        @mkdir($cache_dir, 0755, true);
    }
    
    $clean_adm4 = preg_replace('/[^a-zA-Z0-9_.]/', '', $adm4);
    $cache_file = $cache_dir . 'cuaca_' . $clean_adm4 . '.json';
    $cache_time = 3600; // Cache 1 jam

    // 1. Baca dari cache jika masih berlaku
    if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_time)) {
        $json_data = @file_get_contents($cache_file);
        $data = json_decode($json_data, true);
        if ($data && (!empty($data['data']) || !empty($data['cuaca']))) {
            return $data;
        }
    }

    // 2. Tembak API BMKG jika cache belum ada/kadaluarsa
    $data = get_bmkg_cuaca_adm4($adm4);
    
    if ($data && (!empty($data['data']) || !empty($data['cuaca']))) {
        @file_put_contents($cache_file, json_encode($data));
    }
    
    return $data;
}

// FUNGSI MENGAMBIL ARRAY CUACA REALTIME LENGKAP DENGAN SLOT JAM (WITA)
function get_cuaca_gorontalo_processed() {
    $master_adm4 = get_master_adm4_gorontalo();
    $result = [];

    foreach ($master_adm4 as $item) {
        $raw_api = get_cuaca_adm4_smart_cache($item['adm4']);
        $map_jam = [];
        $cuaca_raw = $raw_api['data'][0]['cuaca'] ?? $raw_api['cuaca'] ?? [];

        if (is_array($cuaca_raw)) {
            foreach ($cuaca_raw as $group) {
                $sub_items = isset($group[0]) ? $group : [$group];
                foreach ($sub_items as $c_item) {
                    if (!is_array($c_item)) continue;
                    
                    $ldt = $c_item['local_datetime'] ?? $c_item['datetime'] ?? '';
                    if (empty($ldt)) continue;

                    $clean_dt = str_replace('T', ' ', $ldt);
                    $parts = explode(' ', $clean_dt);
                    if (isset($parts[1])) {
                        $hour = (int)substr($parts[1], 0, 2);
                        $jam_key = sprintf("%02d:00", $hour);
                        
                        if (!isset($map_jam[$jam_key])) {
                            $map_jam[$jam_key] = [
                                'suhu'    => $c_item['t'] ?? '28',
                                'kondisi' => $c_item['weather_desc'] ?? $c_item['weather_desc_en'] ?? 'Berawan'
                            ];
                        }
                    }
                }
            }
        }

        $result[] = [
            'adm4'      => $item['adm4'],
            'kecamatan' => $item['kec'],
            'kabupaten' => $item['kab'],
            'cuaca'     => $map_jam
        ];
    }

    return $result;
}