<?php
// admin/matakuliah_tambah.php

// Memastikan file konfigurasi dan fungsi di-load
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';

// Cek apakah pengguna sudah login
require_login();

// Cek apakah pengguna adalah admin. Jika tidak, redirect ke halaman login.
if (!is_admin()) {
    header('Location: login.php');
    exit;
}


// Ambil pesan error jika ada (dari proses action sebelumnya)
$error_message = '';
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'invalid') {
        $error_message = 'Gagal menyimpan: Pastikan semua kolom terisi dengan benar.';
    } elseif ($_GET['error'] === 'db_error') {
        $error_message = 'Gagal menyimpan: Terjadi kesalahan database (mungkin Kode Mata Kuliah sudah ada).';
    }
}
?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Tambah Mata Kuliah - Admin | EasyRoom</title>

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
            <h3 class="fw-bold text-primary mb-3">
                <i class="bi bi-book me-2"></i> Tambah Mata Kuliah
            </h3>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger py-2" role="alert">
                            <?= htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>

                    <form action="../backend/admin/matakuliah_action.php" method="post">
                        <input type="hidden" name="action" value="add">

                        <div class="mb-3">
                            <label class="form-label">Kode Mata Kuliah</label>
                            <input type="text" name="kode_mk" class="form-control" required 
                                   placeholder="Contoh: IF1201" maxlength="10">
                            <div class="form-text">Gunakan kode yang unik (misal: gabungan Jurusan dan Nomor).</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Mata Kuliah</label>
                            <input type="text" name="nama_mk" class="form-control" required 
                                   placeholder="Contoh: Pemrograman Web Lanjut">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jumlah SKS</label>
                            <input type="number" name="sks" class="form-control" min="1" max="6" required>
                        </div>

                        <a href="jadwal.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Jadwal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-floppy me-1"></i> Simpan Mata Kuliah
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>