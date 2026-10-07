<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><title>Radar Cuaca - BMKG Gorontalo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body class="bg-light"><div class="container my-4">
<a href="../index.php" class="btn btn-secondary mb-3">&laquo; Kembali ke Beranda</a>
<h3>Radar Cuaca Realtime Gorontalo</h3>
<div id="radar-map" style="height: 500px;" class="shadow-sm rounded"></div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map = L.map('radar-map').setView([0.5401, 123.0600], 9);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; BMKG Gorontalo' }).addTo(map);
</script>
</div></body></html>