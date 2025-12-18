<?php
require_once __DIR__ . '/../../functions.php';
require_once __DIR__ . '/../../config.php';
require_login();
if (!is_admin()) {
    header('Location: ../../login.php');
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// --- HANDLE POST ADD (TAMBAH RUANG) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
    $kode_ruang = $_POST['kode_ruang'] ?? '';
    $nama_ruang = $_POST['nama_ruang'] ?? '';
    $kapasitas = $_POST['kapasitas'] ?? 0;

    if ($kode_ruang === '' || $nama_ruang === '' || (int)$kapasitas <= 0) {
        die('Data tidak lengkap.');
    }

    $stmt = $conn->prepare("INSERT INTO ruang (kode_ruang, nama_ruang, kapasitas) VALUES (?, ?, ?)");
    $stmt->bind_param('ssi', $kode_ruang, $nama_ruang, $kapasitas);
    $stmt->execute();

    header('Location: ../../admin/ruang.php');
    exit;
}

// --- HANDLE POST EDIT (UPDATE RUANG - KOREKSI UNTUK KUOTA) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'edit') {
    $kode_ruang = $_POST['kode_ruang'] ?? ''; // ID yang akan diupdate
    $nama_ruang = $_POST['nama_ruang'] ?? '';
    $kapasitas = $_POST['kapasitas'] ?? 0;

    if ($kode_ruang === '' || $nama_ruang === '' || (int)$kapasitas <= 0) {
        die('Data tidak lengkap untuk diperbarui.');
    }

    // Prepared Statement untuk UPDATE
    $stmt = $conn->prepare("UPDATE ruang SET nama_ruang = ?, kapasitas = ? WHERE kode_ruang = ?");

    // Bind Parameter: 's' (nama_ruang), 'i' (kapasitas), 's' (kode_ruang)
    $stmt->bind_param('sis', $nama_ruang, $kapasitas, $kode_ruang);
    $stmt->execute();

    header('Location: ../../admin/ruang.php');
    exit;
}

// --- HANDLE DELETE VIA GET (KOREKSI FOREIGN KEY) ---
if ($action === 'delete') {
    $kode = $_GET['kode'] ?? '';
    if ($kode === '') {
        header('Location: ../../admin/ruang.php');
        exit;
    }

    // Logika Penghapusan Berantai (untuk menghindari Foreign Key Constraint Fails)
    try {
        // Mulai transaksi
        $conn->begin_transaction();

        // 1. Hapus data anak (peminjaman) yang terkait dengan kode_ruang ini
        $stmt_peminjaman = $conn->prepare("DELETE FROM peminjaman WHERE kode_ruang = ?");
        $stmt_peminjaman->bind_param('s', $kode);
        $stmt_peminjaman->execute();

        // 2. Hapus data induk (ruang)
        $stmt_ruang = $conn->prepare("DELETE FROM ruang WHERE kode_ruang = ?");
        $stmt_ruang->bind_param('s', $kode);
        $stmt_ruang->execute();

        // Commit transaksi jika kedua operasi berhasil
        $conn->commit();
    } catch (Exception $e) {
        // Rollback jika terjadi kegagalan
        $conn->rollback();
        die('Gagal menghapus ruang karena kesalahan sistem: ' . $e->getMessage());
    }

    header('Location: ../../admin/ruang.php');
    exit;
}
// --- END LOGIKA ACTION ---

// =======================================================
// DETAIL VIEW (Sisanya di bawah ini adalah logika Tampilan)
// =======================================================

// Detail view when kode is provided (tetap ada di file ini)
$kode = $_GET['kode'] ?? '';
if ($kode === '') {
    header('Location: ../../admin/ruang.php');
    exit;
}

// Info ruang
$stmt = $conn->prepare("SELECT * FROM ruang WHERE kode_ruang = ?");
$stmt->bind_param("s", $kode);
$stmt->execute();
$ruang = $stmt->get_result()->fetch_assoc();
if (!$ruang) {
    die("Data ruang tidak ditemukan.");
}

// Jadwal yang menggunakan ruangan ini
$stmt2 = $conn->prepare("
    SELECT j.*, mk.nama_matakuliah, d.nama_dosen 
    FROM jadwal j
    JOIN matakuliah mk ON j.kode_matakuliah = mk.kode_matakuliah
    JOIN dosen d ON j.id_dosen = d.id_dosen
    WHERE j.kode_ruang = ?
    ORDER BY j.hari, j.jam_mulai
");
$stmt2->bind_param("s", $kode);
$stmt2->execute();
$jadwal = $stmt2->get_result();
?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Detail Ruang - Admin EasyRoom</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="content">
        <?php include 'header.php'; ?>

        <div class="container py-4">
            <h3 class="fw-bold text-primary mb-3">
                <i class="bi bi-info-circle me-2"></i> Detail Ruang
            </h3>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3"><?= htmlspecialchars($ruang['nama_ruang']); ?></h5>
                            <p class="mb-1"><strong>Kode Ruang:</strong> <?= htmlspecialchars($ruang['kode_ruang']); ?></p>
                            <p class="mb-1"><strong>Kapasitas:</strong> <?= (int)$ruang['kapasitas']; ?> orang</p>
                            <a href="ruang_edit.php?kode=<?= urlencode($ruang['kode_ruang']); ?>" class="btn btn-warning btn-sm mt-3">
                                Edit Ruang
                            </a>
                            <a href="ruang.php" class="btn btn-outline-secondary btn-sm mt-3">
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="fw-semibold text-primary mb-3">Jadwal di Ruangan Ini</h6>

                            <?php if ($jadwal->num_rows === 0): ?>
                                <p class="text-muted mb-0">Belum ada jadwal yang menggunakan ruangan ini.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Hari</th>
                                                <th>Jam</th>
                                                <th>Mata Kuliah</th>
                                                <th>Dosen</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($j = $jadwal->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($j['hari']); ?></td>
                                                    <td><?= safe_time_short($j['jam_mulai']) ?> - <?= safe_time_short($j['jam_selesai']) ?></td>
                                                    <td><?= htmlspecialchars($j['nama_matakuliah']); ?></td>
                                                    <td><?= htmlspecialchars($j['nama_dosen']); ?></td>
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

        </div>
    </div>

</body>

</html>