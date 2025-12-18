<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';
require_login();
if (!is_admin()) {
    header('Location: login.php');
    exit;
}

$kode = $_GET['kode'] ?? '';
if ($kode === '') {
    header('Location: ruang.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM ruang WHERE kode_ruang = ?");
$stmt->bind_param("s", $kode);
$stmt->execute();
$ruang = $stmt->get_result()->fetch_assoc();

if (!$ruang) {
    die("Data ruang tidak ditemukan.");
}

?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Edit Ruang - Admin EasyRoom</title>

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
                <i class="bi bi-pencil-square me-2"></i> Edit Ruang
            </h3>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="../backend/admin/ruang_action.php" method="post">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="kode_ruang" value="<?= htmlspecialchars($ruang['kode_ruang']); ?>">

                        <div class="mb-3">
                            <label class="form-label">Kode Ruang</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($ruang['kode_ruang']); ?>" disabled>
                            <div class="form-text">Kode ruang tidak dapat diubah karena sudah terhubung dengan jadwal/peminjaman.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Ruang</label>
                            <input type="text" name="nama_ruang" class="form-control" required
                                value="<?= htmlspecialchars($ruang['nama_ruang']); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kapasitas (orang)</label>
                            <input type="number" name="kapasitas" class="form-control" min="1" required
                                value="<?= (int)$ruang['kapasitas']; ?>">
                        </div>

                        <a href="ruang.php" class="btn btn-outline-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>