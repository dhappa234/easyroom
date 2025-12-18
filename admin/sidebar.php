<?php
// small helper to detect current file and mark active menu
if (!isset($_SESSION)) session_start();
$current = basename($_SERVER['PHP_SELF']);
function is_active($file)
{
    global $current;
    return $current === $file ? ' active text-primary fw-semibold' : '';
}
?>

<div id="sidebar" class="bg-white shadow-sm p-3 sidebar">
    <h5 class="fw-bold text-primary mb-3">EasyRoom Admin</h5>

    <ul class="list-unstyled">
        <li class="mb-2">
            <a href="../admin/index.php" class="text-dark<?= is_active('index.php'); ?>">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>

        <li class="mb-2">
            <a href="../admin/users.php" class="text-dark<?= is_active('users.php'); ?>">
                <i class="bi bi-people me-2"></i> Kelola Users
            </a>
        </li>

        <li class="mb-2">
            <a href="../admin/ruang.php" class="text-dark<?= is_active('ruang.php'); ?>">
                <i class="bi bi-door-open me-2"></i> Kelola Ruangan
            </a>
        </li>

        <li class="mb-2">
            <a href="../admin/peminjaman.php" class="text-dark<?= is_active('peminjaman.php'); ?>">
                <i class="bi bi-journal-check me-2"></i> Persetujuan Peminjaman
            </a>
        </li>

        <li class="mb-2">
            <a href="../admin/jadwal.php" class="text-dark<?= is_active('jadwal.php'); ?>">
                <i class="bi bi-calendar-week me-2"></i> Jadwal
            </a>
        </li>

        <li class="mb-2">
            <a href="../admin/profil.php" class="text-dark<?= is_active('profil.php'); ?>">
                <i class="bi bi-person-circle me-2"></i> Profil
            </a>
        </li>

    </ul>
</div>

<style>
    :root {
        --sidebar-width: 260px;
    }

    .sidebar {
        width: var(--sidebar-width);
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        transition: left 0.3s ease-in-out;
        overflow-y: auto;
        z-index: 1000;
    }

    .sidebar.collapsed {
        left: calc(var(--sidebar-width) * -1);
    }

    .content {
        margin-left: var(--sidebar-width);
        transition: margin-left 0.3s ease-in-out;
        width: calc(100% - var(--sidebar-width));
    }

    body.sidebar-collapsed .content {
        margin-left: 0;
        width: 100%;
    }
</style>