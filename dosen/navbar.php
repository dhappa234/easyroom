<?php
if (!isset($_SESSION)) session_start();
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';
require_login();
if (!is_dosen()) {
    header('Location: login.php');
    exit;
}
$user = current_user();
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand text-primary fw-bold" href="dashboard.php">
      EasyRoom Dosen
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarDosen" aria-controls="navbarDosen"
            aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarDosen">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

        <li class="nav-item">
          <a class="nav-link" href="dashboard.php">
            <i class="bi bi-columns-gap me-1"></i> Dashboard
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="ketersediaan_ruang.php">
            <i class="bi bi-door-open me-1"></i> Ketersediaan Ruang
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="notifikasi.php">
            <i class="bi bi-bell me-1"></i> Notifikasi
          </a>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-primary" href="#"
             id="dropdownDosen" role="button" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle me-1"></i>
            <?= htmlspecialchars($user['nama']); ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownDosen">
            <li><a class="dropdown-item" href="profil.php">Profil</a></li>
            <li><a class="dropdown-item text-danger" href="../logout.php">Logout</a></li>
          </ul>
        </li>

      </ul>
    </div>
  </div>
</nav>
