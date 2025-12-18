<?php
// admin/jadwal.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

require_login();
if (!is_admin()) {
    header('Location: login.php');
    exit;
}

// Ambil semua jadwal + join informasi matakuliah, dosen, ruang
$sql = "
    SELECT 
        j.*,
        mk.nama_mk,
        d.nama_dosen,
        r.nama_ruang
    FROM jadwal j
    LEFT JOIN matakuliah mk ON j.kode_mk   = mk.kode_mk
    LEFT JOIN dosen      d  ON j.id_dosen  = d.id_dosen
    LEFT JOIN ruang      r  ON j.kode_ruang = r.kode_ruang
    ORDER BY 
        FIELD(j.hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'),
        j.jam_mulai
";
$jadwal = $conn->query($sql);
?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Kelola Jadwal - Admin | EasyRoom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

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

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="fw-bold text-primary mb-1">
                        <i class="bi bi-calendar-week me-2"></i> Kelola Jadwal Kuliah
                    </h3>
                    <p class="text-muted mb-0">
                        Tambah dan kelola jadwal perkuliahan yang akan ditampilkan ke dosen & mahasiswa.
                    </p>
                </div>
                <a href="jadwal_tambah.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Jadwal
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'created'): ?>
                        <div class="alert alert-success py-2">
                            Jadwal berhasil ditambahkan.
                        </div>
                    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
                        <div class="alert alert-success py-2">
                            Jadwal berhasil dihapus.
                        </div>
                    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
                        <div class="alert alert-success py-2">
                            Jadwal berhasil diperbarui.
                        </div>
                    <?php endif; ?>

                    <?php if ($jadwal->num_rows === 0): ?>
                        <p class="text-muted mb-0">Belum ada data jadwal.</p>
                    <?php else: ?>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Kode</th>
                                        <th>Mata Kuliah</th>
                                        <th>Dosen</th>
                                        <th>Ruangan</th>
                                        <th>Hari</th>
                                        <th>Waktu Mulai</th>
                                        <th>Kelas</th>
                                        <th>Semester</th>
                                        <th>Kuota</th>
                                        <th style="width: 80px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $jadwal->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['kode_jadwal']); ?></td>
                                            <td><?= htmlspecialchars($row['nama_mk'] ?? '-'); ?></td>
                                            <td><?= htmlspecialchars($row['nama_dosen'] ?? '-'); ?></td>
                                            <td><?= htmlspecialchars($row['nama_ruang'] ?? '-'); ?></td>
                                            <td><?= htmlspecialchars($row['hari'] ?? '-'); ?></td>
                                            <td>
                                                <?php
                                                if (!empty($row['jam_mulai'])) {
                                                    echo safe_time_short($row['jam_mulai']);
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                            <td><?= htmlspecialchars($row['kelas'] ?? '-'); ?></td>
                                            <td><?= htmlspecialchars($row['semester'] ?? '-'); ?></td>
                                            <td><?= (int)($row['kuota_peserta'] ?? 0); ?></td>
                                            <td>
                                                <a href="jadwal_tambah.php?kode=<?= urlencode($row['kode_jadwal']); ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="../backend/admin/jadwal_action.php?action=delete&kode=<?= urlencode($row['kode_jadwal']); ?>"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Yakin ingin menghapus jadwal ini?');">
                                                    <i class="bi bi-trash"></i>
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
            <div class="card-footer text-end border-0 pt-0 mt-3">
                <a href="matakuliah_tambah.php" class="btn btn-outline-secondary">
                    <i class="bi bi-book me-1"></i> Tambah Mata Kuliah
                </a>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>