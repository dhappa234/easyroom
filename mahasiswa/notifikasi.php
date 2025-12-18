<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';
require_login();
if (!is_mahasiswa()) {
    header("Location: login.php");
    exit;
}

$user = current_user();

// Jika nanti ada tabel notifikasi khusus mahasiswa, query bisa ditambahkan di sini.
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Notifikasi - Mahasiswa</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body class="mhs-page">

<?php include 'navbar.php'; ?>

<div class="container mhs-container">

    <div class="page-header">
        <h3 class="page-header-title mb-1">
            <i class="bi bi-bell me-2"></i> Notifikasi
        </h3>
        <p class="page-header-subtitle mb-0">
            Halaman ini akan menampilkan informasi penting terkait jadwal atau pengumuman dari fakultas.
        </p>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <p class="text-muted mb-0">
                Belum ada notifikasi untuk saat ini.
            </p>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
