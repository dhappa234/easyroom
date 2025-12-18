<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';
require_login();
if (!is_admin()) {
    header('Location: login.php');
    exit;
}

// total dosen
$total_dosen = $conn->query("SELECT COUNT(*) as jml FROM dosen")->fetch_assoc()['jml'];

// total mahasiswa
$total_mhs = $conn->query("SELECT COUNT(*) as jml FROM mahasiswa")->fetch_assoc()['jml'];

// total ruang
$total_ruang = $conn->query("SELECT COUNT(*) as jml FROM ruang")->fetch_assoc()['jml'];

// peminjaman menunggu
$waiting = $conn->query("SELECT COUNT(*) as jml FROM peminjaman WHERE status='Menunggu'")->fetch_assoc()['jml'];
?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Dashboard Admin - EasyRoom</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="content">
        <?php include 'header.php'; ?>

        <div class="container py-4">

            <h3 class="fw-bold text-primary mb-4">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard Admin
            </h3>

            <div class="row g-4">

                <div class="col-md-3">
                    <div class="card shadow-sm p-3">
                        <h6 class="text-muted">Total Dosen</h6>
                        <h3 class="fw-bold text-primary"><?= $total_dosen; ?></h3>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm p-3">
                        <h6 class="text-muted">Total Mahasiswa</h6>
                        <h3 class="fw-bold text-primary"><?= $total_mhs; ?></h3>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm p-3">
                        <h6 class="text-muted">Total Ruangan</h6>
                        <h3 class="fw-bold text-primary"><?= $total_ruang; ?></h3>
                    </div>
                </div>

                <div class="col-md-3">
                    <a href="/admin/peminjaman.php" class="text-decoration-none">
                        <div class="card shadow-sm p-3">
                            <h6 class="text-muted">Menunggu Persetujuan</h6>
                            <h3 class="fw-bold text-warning"><?= $waiting; ?></h3>
                        </div>
                    </a>
                </div>

            </div>

        </div>

    </div>
</body>

</html>