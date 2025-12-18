<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';
require_login();
if (!is_dosen()) {
    header('Location: login.php');
    exit;
}

$data = $_SESSION['peminjaman_draft'] ?? null;
if (!$data) {
    header('Location: /dosen/ketersediaan_ruang.php');
    exit;
}

// possible errors set by backend process
$db_error = null;
if (isset($_GET['error']) && isset($_SESSION['db_error_message'])) {
    $db_error = $_SESSION['db_error_message'];
    unset($_SESSION['db_error_message']); // Clear it after displaying
}

$peminjaman_error = null;
if (isset($_SESSION['peminjaman_error'])) {
    $peminjaman_error = $_SESSION['peminjaman_error'];
    unset($_SESSION['peminjaman_error']);
}

// Ambil info ruang
$stmt = $conn->prepare("SELECT nama_ruang FROM ruang WHERE kode_ruang = ?");
$stmt->bind_param("s", $data['kode_ruang']);
$stmt->execute();
$ruang = $stmt->get_result()->fetch_assoc();
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Ajukan Peminjaman - Tahap 2</title>

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
                <i class="bi bi-check2-square me-2"></i>Konfirmasi Pengajuan (Tahap 2)
            </h3>
            <p class="page-header-subtitle mb-0">
                Periksa kembali data peminjaman sebelum dikirim ke admin.
            </p>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">

                <?php if ($db_error): ?>
                    <div class="alert alert-danger">
                        <strong>Database Error:</strong><br>
                        <pre><?= htmlspecialchars($db_error); ?></pre>
                    </div>
                <?php endif; ?>

                <?php if ($peminjaman_error): ?>
                    <div class="alert alert-warning">
                        <?= htmlspecialchars($peminjaman_error); ?>
                    </div>
                <?php endif; ?>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width:30%;">Ruang</th>
                            <td><?= htmlspecialchars($ruang['nama_ruang']); ?></td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td><?= htmlspecialchars($data['tanggal']); ?></td>
                        </tr>
                        <tr>
                            <th>Waktu</th>
                            <td><?= htmlspecialchars($data['waktu_mulai']); ?> -
                                <?= htmlspecialchars($data['waktu_selesai']); ?></td>
                        </tr>
                        <tr>
                            <th>Keperluan</th>
                            <td><?= nl2br(htmlspecialchars($data['keperluan'])); ?></td>
                        </tr>
                        <tr>
                            <th>File Pengajuan</th>
                            <td>
                                <?php if (!empty($data['file_pengajuan'])): ?>
                                    <a href="/<?= htmlspecialchars($data['file_pengajuan']); ?>"
                                        target="_blank">Lihat File</a>
                                <?php else: ?>
                                    <span class="text-muted">Tidak ada file.</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="pengajuan_step1.php?ruang=<?= urlencode($data['kode_ruang']); ?>"
                        class="btn btn-outline-secondary">
                        Kembali
                    </a>

                    <form method="post" action="../backend/admin/pengajuan_step2_process.php">
                        <button type="submit" class="btn btn-primary btn-anim">Kirim Pengajuan</button>
                    </form>

                </div>

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>