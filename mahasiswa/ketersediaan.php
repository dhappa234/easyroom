<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';
require_login();
if (!is_mahasiswa()) {
    header("Location: login.php");
    exit;
}

$ruang = $conn->query("SELECT * FROM ruang ORDER BY nama_ruang");
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Ketersediaan Ruangan - Mahasiswa</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body class="mhs-page">

    <?php include 'navbar.php'; ?>

    <div class="container mhs-container py-4">

        <div class="page-header">
            <h3 class="page-header-title mb-1">
                <i class="bi bi-door-open me-2"></i> Ketersediaan Ruangan
            </h3>
            <p class="page-header-subtitle mb-0 text-muted">Cek ruangan dan kapasitasnya.</p>
        </div>

        <div class="row g-3">

            <?php while ($r = $ruang->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="hover-card p-3">
                        <h5 class="fw-semibold"><?= $r['nama_ruang']; ?></h5>
                        <p class="text-muted small">Kapasitas: <?= $r['kapasitas']; ?> orang</p>

                        <a href="ruang_detail.php?kode=<?= $r['kode_ruang']; ?>"
                            class="btn btn-outline-primary btn-sm">
                            Lihat Detail Ruangan
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>