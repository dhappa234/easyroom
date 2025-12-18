<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';
require_login();
if (!is_admin()) {
    header('Location: login.php');
    exit;
}

$ruang = $conn->query("SELECT * FROM ruang ORDER BY nama_ruang");
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Kelola Ruangan - Admin EasyRoom</title>

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
                <i class="bi bi-door-open me-2"></i> Kelola Ruangan
            </h3>

            <div class="d-flex justify-content-between mb-3">
                <p class="text-muted mb-0">Daftar semua ruangan yang digunakan dalam jadwal dan peminjaman.</p>
                <a href="ruang_add.php" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Ruang
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <?php if ($ruang->num_rows === 0): ?>
                        <p class="text-muted mb-0">Belum ada data ruangan.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Kode Ruang</th>
                                        <th>Nama Ruang</th>
                                        <th>Kapasitas</th>
                                        <th style="width: 180px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($r = $ruang->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($r['kode_ruang']); ?></td>
                                            <td><?= htmlspecialchars($r['nama_ruang']); ?></td>
                                            <td><?= (int)$r['kapasitas']; ?> orang</td>
                                            <td>
                                                <a href="ruang_detail.php?kode=<?= urlencode($r['kode_ruang']); ?>"
                                                    class="btn btn-info btn-sm text-white">
                                                    Detail
                                                </a>
                                                <a href="ruang_edit.php?kode=<?= urlencode($r['kode_ruang']); ?>"
                                                    class="btn btn-warning btn-sm">
                                                    Edit
                                                </a>
                                                <a href="../backend/admin/ruang_action.php?action=delete&kode=<?= urlencode($r['kode_ruang']); ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Yakin ingin menghapus ruang ini? Data jadwal/peminjaman yang terkait bisa ikut terdampak.');">
                                                    Hapus
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