<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../functions.php';

require_login();
if (!is_mahasiswa()) {
    header('Location: login.php');
    exit;
}

$user = current_user();

// helper kecil untuk kasih class "active" di menu yang sedang dibuka
$current = basename($_SERVER['PHP_SELF']);
function is_active($file)
{
    global $current;
    return $current === $file ? ' active text-primary fw-semibold' : '';
}
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">

        <a class="navbar-brand fw-bold text-primary" href="index.php">
            EasyRoom Mahasiswa
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarMahasiswa" aria-controls="navbarMahasiswa"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMahasiswa">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link<?= is_active('index.php'); ?>" href="index.php">
                        <i class="bi bi-columns-gap me-1"></i> Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link<?= is_active('jadwal.php'); ?>" href="jadwal.php">
                        <i class="bi bi-calendar-week me-1"></i> Jadwal
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link<?= is_active('ketersediaan.php'); ?>" href="ketersediaan.php">
                        <i class="bi bi-door-open me-1"></i> Ketersediaan Ruang
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link<?= is_active('notifikasi.php'); ?>" href="notifikasi.php">
                        <i class="bi bi-bell me-1"></i> Notifikasi
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-primary" href="#"
                        id="dropdownMahasiswa" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= htmlspecialchars($user['nama'] ?? 'Mahasiswa'); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMahasiswa">
                        <li><a class="dropdown-item" href="profil.php">Profil</a></li>
                        <li><a class="dropdown-item text-danger" href="../logout.php">Logout</a></li>
                    </ul>
                </li>

            </ul>
        </div>

    </div>
</nav>