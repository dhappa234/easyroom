<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';
require_login();
if (!is_admin()) {
    header('Location: login.php');
    exit;
}

// Fungsi generate Kode Ruang otomatis: Prefix + 3 Angka Acak
function generate_auto_kode_ruang()
{
    $prefix = 'RM';
    // Menghasilkan 3 angka acak
    $random_numbers = substr(str_shuffle('0123456789'), 0, 3);
    return $prefix . $random_numbers;
}
$auto_kode = generate_auto_kode_ruang();
?>


<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Tambah Ruang - Admin EasyRoom</title>

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
                <i class="bi bi-plus-circle me-2"></i> Tambah Ruang
            </h3>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="../backend/admin/ruang_action.php" method="post">
                        <input type="hidden" name="action" value="add">

                        <div class="mb-3">
                            <label class="form-label">Kode Ruang</label>
                            <input type="text" name="kode_ruang" class="form-control bg-light" value="<?= $auto_kode; ?>" readonly >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Ruang</label>
                            <input type="text" name="nama_ruang" class="form-control" required placeholder="Misal: Laboratorium Komputer 1">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kapasitas (orang)</label>
                            <input type="number" name="kapasitas" class="form-control" min="1" required>
                        </div>

                        <a href="ruang.php" class="btn btn-outline-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>