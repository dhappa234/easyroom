<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';
require_login();
if (!is_mahasiswa()) {
    header("Location: login.php");
    exit;
}

$user = current_user();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Dashboard Mahasiswa - EasyRoom</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body class="mhs-page">

<?php include 'navbar.php'; ?>

<div class="container mhs-container">

    <!-- HEADER -->
    <div class="page-header">
        <h3 class="page-header-title mb-1">
            Hai, <?= htmlspecialchars($user['nama']); ?> 👋
        </h3>
        <p class="page-header-subtitle mb-0">
            Selamat datang di <strong>EasyRoom Mahasiswa</strong>.  
            Di sini kamu bisa melihat jadwal kuliah dan ketersediaan ruangan di fakultasmu.
        </p>
    </div>

    <!-- 3 KARTU MENU UTAMA -->
    <div class="row g-4 mt-1">

        <div class="col-md-4">
            <a href="jadwal.php" class="text-decoration-none">
                <div class="hover-card p-4 mhs-feature-card">
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-3">
                            <div class="feature-icon">
                                <i class="bi bi-calendar-week"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="fw-bold text-primary mb-0">Jadwal Kuliah</h5>
                            <small class="text-muted">Lihat jadwal perkuliahanmu.</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-0 mt-2">
                        Jadwal yang tampil sudah terhubung dengan mata kuliah yang kamu ambil.
                    </p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="ketersediaan.php" class="text-decoration-none">
                <div class="hover-card p-4 mhs-feature-card">
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-3">
                            <div class="feature-icon">
                                <i class="bi bi-door-open"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="fw-bold text-primary mb-0">Ketersediaan Ruang</h5>
                            <small class="text-muted">Cek ruangan dan kapasitasnya.</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-0 mt-2">
                        Berguna saat kamu ingin tahu ruangan mana yang sedang dipakai untuk kuliah.
                    </p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="profil.php" class="text-decoration-none">
                <div class="hover-card p-4 mhs-feature-card">
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-3">
                            <div class="feature-icon">
                                <i class="bi bi-person-circle"></i>
                            </div>
                        </div>
                        <div>
                            <h5 class="fw-bold text-primary mb-0">Profil & Akun</h5>
                            <small class="text-muted">Lihat data diri dan ganti password.</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-0 mt-2">
                        Pastikan data akunmu aman dan gunakan password yang kuat.
                    </p>
                </div>
            </a>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
