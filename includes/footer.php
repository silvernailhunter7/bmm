<footer class="bg-white border-top py-4 mt-5">
    <div class="container-fluid px-lg-5">
        <div class="row align-items-center g-3">

            <div class="col-md-7 col-lg-8 text-center text-md-start">
                <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 mb-1">
                    <img src="https://www.bmkg.go.id/asset/img/logo/logo-bmkg.png" height="28" alt="BMKG">
                    <strong class="text-dark small">Stasiun Meteorologi & Geofisika BMKG Gorontalo</strong>
                </div>
                <p class="mb-0 text-muted small" style="font-size: 0.8rem;">
                    &copy; <?= date('Y') ?> BMKG Provinsi Gorontalo. All Rights Reserved.
                </p>
            </div>

            <div class="col-md-5 col-lg-4">
                <div class="card border-0 bg-light p-2.5 rounded-3 ms-md-auto" style="max-width: 380px;">
                    <div class="fw-bold text-dark mb-2 text-center text-md-start" style="font-size: 0.78rem;">
                        <i class="bi bi-headset text-primary me-1"></i> Layanan Informasi / WhatsApp Stasiun:
                    </div>
                    
                    <div class="vstack gap-1">
                        <!-- Stasiun Meteorologi Djalaluddin -->
                        <a href="https://wa.me/628114321196" target="_blank" class="text-decoration-none d-flex justify-content-between align-items-center px-2 py-1 bg-white rounded border text-dark hover-shadow small" style="font-size: 0.75rem;">
                            <span class="fw-semibold text-truncate"><i class="bi bi-cloud-sun text-warning me-1"></i> Stamet Djalaluddin</span>
                            <span class="badge text-bg-success rounded-pill px-2 py-1"><i class="bi bi-whatsapp me-1"></i> 0811-4321-196</span>
                        </a>

                        <!-- Stasiun Geofisika Gorontalo -->
                        <a href="https://wa.me/628114322196" target="_blank" class="text-decoration-none d-flex justify-content-between align-items-center px-2 py-1 bg-white rounded border text-dark hover-shadow small" style="font-size: 0.75rem;">
                            <span class="fw-semibold text-truncate"><i class="bi bi-activity text-danger me-1"></i> Stageof Gorontalo</span>
                            <span class="badge text-bg-success rounded-pill px-2 py-1"><i class="bi bi-whatsapp me-1"></i> 0811-4322-196</span>
                        </a>

                        <!-- Stasiun Klimatologi Gorontalo -->
                        <a href="https://wa.me/628114323196" target="_blank" class="text-decoration-none d-flex justify-content-between align-items-center px-2 py-1 bg-white rounded border text-dark hover-shadow small" style="font-size: 0.75rem;">
                            <span class="fw-semibold text-truncate"><i class="bi bi-bar-chart-line text-info me-1"></i> Staklim Gorontalo</span>
                            <span class="badge text-bg-success rounded-pill px-2 py-1"><i class="bi bi-whatsapp me-1"></i> 0811-4323-196</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</body>
</html>