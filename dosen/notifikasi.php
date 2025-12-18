<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';
require_login();
if (!is_dosen()) {
    header('Location: login.php');
    exit;
}

$user = current_user();
$id_dosen = $user['id'];

$stmt = $conn->prepare("
    SELECT p.*, r.kode_ruang
    FROM peminjaman p
    JOIN ruang r ON p.kode_ruang = r.kode_ruang
    WHERE p.id_dosen = ?
    ORDER BY p.tanggal DESC, p.waktu_mulai DESC
");
$stmt->bind_param("i", $id_dosen);
$stmt->execute();
$notif = $stmt->get_result();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Notifikasi Peminjaman - Dosen</title>

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
            <i class="bi bi-bell me-2"></i>Notifikasi Peminjaman Ruangan
        </h3>
        <p class="page-header-subtitle mb-0">
            Lihat status pengajuan peminjaman ruangan yang telah Anda kirim.
        </p>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <?php if ($notif->num_rows === 0): ?>
                <p class="text-muted mb-0">
                    Belum ada pengajuan peminjaman ruangan.
                </p>
            <?php else: ?>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>Ruang</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Keperluan</th>
                            <th>Status</th>
                            <th>Catatan Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($n = $notif->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($n['kode_ruang']); ?></td>
                            <td><?= htmlspecialchars($n['tanggal']); ?></td>
                            <td><?= htmlspecialchars($n['waktu_mulai']); ?> -
                                <?= htmlspecialchars($n['waktu_selesai']); ?></td>
                            <td><?= nl2br(htmlspecialchars($n['keperluan'])); ?></td>
                            <td>
                                <?php if ($n['status'] === 'Menunggu'): ?>
                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                <?php elseif ($n['status'] === 'Disetujui'): ?>
                                    <span class="badge bg-success">Disetujui</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Ditolak</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $n['catatan_admin']
                                    ? nl2br(htmlspecialchars($n['catatan_admin']))
                                    : '<span class="text-muted">-</span>'; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <?php endif; ?>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
