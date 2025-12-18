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

// 1. Ambil data ruangan
$stmt = $conn->prepare("SELECT * FROM ruang WHERE kode_ruang = ?");
$stmt->bind_param("s", $kode);
$stmt->execute();
$ruang = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$ruang) {
    die("Data ruang tidak ditemukan.");
}

// 2. Ambil jadwal penggunaan rutin (Kuliah)
$sqlJadwal = "
    SELECT j.*, mk.nama_mk, d.nama_dosen
    FROM jadwal j
    LEFT JOIN matakuliah mk ON j.kode_mk = mk.kode_mk
    LEFT JOIN dosen d ON j.id_dosen = d.id_dosen
    WHERE j.kode_ruang = ?
    ORDER BY FIELD(j.hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'), j.jam_mulai
";
$stmtJ = $conn->prepare($sqlJadwal);
$stmtJ->bind_param("s", $kode);
$stmtJ->execute();
$jadwal = $stmtJ->get_result();
$stmtJ->close();

// 3. Ambil peminjaman yang telah disetujui (7 hari ke depan)
$stmtP = $conn->prepare("
    SELECT p.*, d.nama_dosen AS nama_dosen_peminjam
    FROM peminjaman p
    LEFT JOIN dosen d ON p.id_dosen = d.id_dosen
    WHERE p.kode_ruang = ?
      AND p.status = 'Disetujui'
      AND p.tanggal BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 6 DAY)
    ORDER BY p.tanggal, p.waktu_mulai
");
$stmtP->bind_param("s", $kode);
$stmtP->execute();
$peminjaman = $stmtP->get_result();
$stmtP->close();
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Detail Ruang - Admin EasyRoom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

    <?php include 'sidebar.php'; ?>

    <div class="content">
        <?php include 'header.php'; ?>

        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="ruang.php">Ruangan</a></li>
                            <li class="breadcrumb-item active">Detail Ruang</li>
                        </ol>
                    </nav>
                    <h3 class="fw-bold text-primary mb-0">
                        <i class="bi bi-info-square-fill me-2"></i>Detail Ruangan
                    </h3>
                </div>
                <div class="text-end">
                    <span id="room-status-badge" class="badge rounded-pill mb-1 p-2 px-3">Memuat...</span>
                    <div id="room-status-detail" class="small text-muted"></div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0 fw-bold"><i class="bi bi-card-text me-2"></i>Identitas Ruang</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="text-muted small text-uppercase fw-bold">Nama Ruangan</label>
                                <p class="fs-5 fw-semibold text-dark"><?= htmlspecialchars($ruang['nama_ruang']); ?></p>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="text-muted small text-uppercase fw-bold">Kode</label>
                                    <p class="fw-bold text-primary"><?= htmlspecialchars($ruang['kode_ruang']); ?></p>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="text-muted small text-uppercase fw-bold">Kapasitas</label>
                                    <p><i class="bi bi-people me-1"></i> <?= (int)$ruang['kapasitas']; ?> Orang</p>
                                </div>
                            </div>
                            <hr>
                            <div class="d-grid gap-2">
                                <a href="ruang_edit.php?kode=<?= urlencode($ruang['kode_ruang']); ?>" class="btn btn-warning">
                                    <i class="bi bi-pencil-square me-1"></i> Edit Ruangan
                                </a>
                                <a href="ruang.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white">
                            <ul class="nav nav-tabs card-header-tabs" id="roomTab" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active fw-semibold" id="jadwal-tab" data-bs-toggle="tab" data-bs-target="#jadwal-pane" type="button" role="tab">
                                        Jadwal Rutin (Kuliah)
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link fw-semibold" id="peminjaman-tab" data-bs-toggle="tab" data-bs-target="#peminjaman-pane" type="button" role="tab">
                                        Booking Mendatang
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="roomTabContent">
                                <div class="tab-pane fade show active" id="jadwal-pane" role="tabpanel">
                                    <?php if ($jadwal->num_rows === 0): ?>
                                        <div class="text-center py-5">
                                            <i class="bi bi-calendar-x text-muted fs-1"></i>
                                            <p class="text-muted mt-2">Tidak ada jadwal kuliah rutin di ruangan ini.</p>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle">
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
                                                            <td><span class="badge bg-soft-primary text-primary"><?= htmlspecialchars($j['hari']); ?></span></td>
                                                            <td class="small fw-bold"><?= safe_time_short($j['jam_mulai']); ?> - <?= safe_time_short($j['jam_selesai']); ?></td>
                                                            <td><?= htmlspecialchars($j['nama_mk']); ?></td>
                                                            <td class="text-muted small"><?= htmlspecialchars($j['nama_dosen']); ?></td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="tab-pane fade" id="peminjaman-pane" role="tabpanel">
                                    <?php if ($peminjaman->num_rows === 0): ?>
                                        <div class="text-center py-5">
                                            <i class="bi bi-bookmark-check text-muted fs-1"></i>
                                            <p class="text-muted mt-2">Belum ada peminjaman yang disetujui dalam 7 hari ke depan.</p>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Tanggal</th>
                                                        <th>Waktu</th>
                                                        <th>Dosen Peminjam</th>
                                                        <th>Keperluan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($p = $peminjaman->fetch_assoc()): ?>
                                                        <tr>
                                                            <td class="fw-bold"><?= date('d M Y', strtotime($p['tanggal'])); ?></td>
                                                            <td class="small text-primary fw-bold"><?= safe_time_short($p['waktu_mulai']); ?> - <?= safe_time_short($p['waktu_selesai']); ?></td>
                                                            <td><?= htmlspecialchars($p['nama_dosen_peminjam']); ?></td>
                                                            <td class="small text-muted"><?= htmlspecialchars($p['keperluan']); ?></td>
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script untuk real-time status (sudah ada di kode lama, dipertahankan)
        (function() {
            const kode = <?= json_encode($kode); ?>;
            const badge = document.getElementById('room-status-badge');
            const detail = document.getElementById('room-status-detail');

            function render(data) {
                if (!data) return;
                if (data.status === 'unavailable') {
                    badge.className = 'badge rounded-pill bg-danger p-2 px-3';
                    badge.innerHTML = '<i class="bi bi-lock-fill me-1"></i> Tidak tersedia';
                    detail.textContent = data.current ? (data.current.nama_matakuliah || data.current.keperluan || '') : '';
                } else {
                    badge.className = 'badge rounded-pill bg-success p-2 px-3';
                    badge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Tersedia';
                    detail.textContent = data.next ? 'Booking Berikutnya: ' + data.next.waktu_mulai : 'Ruangan Kosong';
                }
            }

            function fetchStatus() {
                fetch('../room_status.php?kode=' + encodeURIComponent(kode))
                    .then(r => r.json()).then(render).catch(e => {
                        badge.className = 'badge bg-secondary';
                        badge.textContent = 'Status Offline';
                    });
            }
            fetchStatus();
            setInterval(fetchStatus, 60000); 
        })();
    </script>
</body>
</html>