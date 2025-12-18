<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';
require_login();
if (!is_dosen()) {
    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Peminjaman Berhasil - Dosen</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body class="mhs-page">

<?php include 'navbar.php'; ?>

<div class="container mhs-container text-center">

    <div class="page-header">
        <h3 class="page-header-title mb-1">
            Pengajuan Berhasil Dikirim
        </h3>
        <p class="page-header-subtitle mb-0">
            Permohonan peminjaman ruang Anda telah dikirim dan menunggu persetujuan admin.
        </p>
    </div>

    <div class="mb-4">
        <i class="bi bi-check-circle-fill text-success"
           style="font-size: 70px;"></i>
    </div>

    <a href="dashboard.php" class="btn btn-primary btn-anim">
        Kembali ke Dashboard
    </a>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
