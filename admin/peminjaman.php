<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';
require_login();
if (!is_admin()) {
    header('Location: login.php');
    exit;
}

// Ambil semua peminjaman
$sql = "
    SELECT p.*, r.nama_ruang, d.nama_dosen 
    FROM peminjaman p
    JOIN ruang r ON p.kode_ruang = r.kode_ruang
    JOIN dosen d ON p.id_dosen = d.id_dosen
    ORDER BY 
        FIELD(p.status, 'Menunggu', 'Disetujui', 'Ditolak'),
        p.tanggal DESC, p.waktu_mulai DESC
";
$peminjaman = $conn->query($sql);
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Persetujuan Peminjaman - Admin EasyRoom</title>
    
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
                <i class="bi bi-journal-check me-2"></i> Persetujuan Peminjaman Ruang
            </h3>

            <p class="text-muted mb-3">
                Lihat dan kelola semua pengajuan peminjaman ruang yang diajukan oleh dosen.
            </p>

            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <?php if ($peminjaman->num_rows === 0): ?>
                        <p class="text-muted mb-0">Belum ada pengajuan peminjaman.</p>
                    <?php else: ?>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Ruang</th>
                                        <th>Dosen</th>
                                        <th>Keperluan</th>
                                        <th>Status</th>
                                        <th style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($p = $peminjaman->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($p['tanggal']); ?></td>
                                            <td><?= htmlspecialchars($p['waktu_mulai']); ?> - <?= htmlspecialchars($p['waktu_selesai']); ?></td>
                                            <td><?= htmlspecialchars($p['nama_ruang']); ?></td>
                                            <td><?= htmlspecialchars($p['nama_dosen']); ?></td>
                                            <td><?= htmlspecialchars($p['keperluan']); ?></td>
                                            <td>
                                                <?php if ($p['status'] === 'Menunggu'): ?>
                                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                                <?php elseif ($p['status'] === 'Disetujui'): ?>
                                                    <span class="badge bg-success">Disetujui</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Ditolak</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="peminjaman_detail.php?id=<?= $p['kode_peminjaman']; ?>"
                                                    class="btn btn-sm btn-info text-white">
                                                    Detail
                                                </a>
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

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>