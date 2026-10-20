<?php
// Proxy untuk melewati proteksi hotlink server BMKG
$url = "http://202.90.198.22/IMAGE/ANIMASI/H08_EH_Gorontalo_m18.gif";

header('Content-Type: image/png');
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
$img = curl_exec($ch);
curl_close($ch);

echo $img;
?>