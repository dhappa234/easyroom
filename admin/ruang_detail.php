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

// Info ruang
$stmt = $conn->prepare("SELECT * FROM ruang WHERE kode_ruang = ?");
$stmt->bind_param("s", $kode);
$stmt->execute();
$ruang = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$ruang) {
    die("Data ruang tidak ditemukan.");
}

// Ambil jadwal penggunaan ruangan ini
$sql = "
    SELECT
        j.*,
        mk.nama_mk AS nama_matakuliah,
        d.nama_dosen AS nama_dosen
    FROM jadwal j
    LEFT JOIN matakuliah mk ON j.kode_mk = mk.kode_mk
    LEFT JOIN dosen d ON j.id_dosen = d.id_dosen
    WHERE j.kode_ruang = ?
    ORDER BY FIELD(j.hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'), j.jam_mulai
";
$stmt2 = $conn->prepare($sql);
$stmt2->bind_param("s", $kode);
$stmt2->execute();
$jadwal = $stmt2->get_result();
$stmt2->close();

// Ambil peminjaman yang telah disetujui untuk ruangan ini (hari ini .. +6 hari)
$stmt3 = $conn->prepare("
    SELECT p.*, d.nama_dosen AS nama_dosen_peminjam
    FROM peminjaman p
    LEFT JOIN dosen d ON p.id_dosen = d.id_dosen
    WHERE p.kode_ruang = ?
      AND p.status = 'Disetujui'
      AND p.tanggal BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 6 DAY)
    ORDER BY p.tanggal, p.waktu_mulai
");
$stmt3->bind_param("s", $kode);
$stmt3->execute();
$peminjaman = $stmt3->get_result();
$stmt3->close();
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

<body>
    <?php include 'sidebar.php'; ?>

    <div class="content">
        <?php include 'header.php'; ?>

        <div class="container py-4">

            <div class="page-header mb-4 d-flex justify-content-between align-items-start">
                <div>
                    <h3 class="mb-1"><i class="bi bi-door-open me-2"></i> Detail Ruang</h3>
                    <p class="text-muted mb-0">Informasi ruangan, jadwal perkuliahan, dan peminjaman terjadwal.</p>
                </div>
                <div class="text-end">
                    <span id="room-status-badge" class="badge bg-secondary">Memuat status...</span>
                </div>
                <div>
                    <a href="ruang.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
                    <a href="ruang_edit.php?kode=<?= urlencode($ruang['kode_ruang']); ?>" class="btn btn-primary btn-sm">Edit Ruang</a>
                </div>
            </div>

            <div class="row g-4">

                <div class="col-md-4">
                    <div class="card shadow-sm border-0 p-3">
                        <h5 class="fw-bold text-primary mb-2"><?= htmlspecialchars($ruang['nama_ruang'] ?? '-'); ?></h5>
                        <p class="mb-1"><strong>Kode Ruang:</strong><br><?= htmlspecialchars($ruang['kode_ruang'] ?? '-'); ?></p>
                        <p class="mb-1"><strong>Kapasitas:</strong><br><?= (int)($ruang['kapasitas'] ?? 0); ?> orang</p>
                        <p class="mb-1"><strong>Lokasi:</strong><br><?= htmlspecialchars($ruang['lokasi'] ?? '-'); ?></p>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">

                            <h5 class="fw-semibold text-primary mb-3">Jadwal Penggunaan Ruangan</h5>

                            <?php if ($jadwal->num_rows === 0): ?>
                                <p class="text-muted mb-0">Belum ada jadwal perkuliahan yang tercatat untuk ruangan ini.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle mb-0">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>Hari</th>
                                                <th>Waktu</th>
                                                <th>Mata Kuliah</th>
                                                <th>Dosen</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $maxDt = new DateTime('+6 days');
                                            while ($row = $jadwal->fetch_assoc()):
                                                $nextDate = get_next_date_for_day($row['hari'] ?? '');
                                                $nextDt = new DateTime($nextDate);
                                                if ($nextDt > $maxDt) continue;
                                            ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($row['hari'] ?? '-'); ?></td>
                                                    <td><?= safe_time_short($row['jam_mulai'] ?? ''); ?> &ndash; <?= safe_time_short($row['jam_selesai'] ?? ''); ?></td>
                                                    <td><?= htmlspecialchars($row['nama_matakuliah'] ?? '-'); ?></td>
                                                    <td><?= htmlspecialchars($row['nama_dosen'] ?? '-'); ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>

                            <hr>

                            <h5 class="fw-semibold text-primary mb-3">Peminjaman Terjadwal (Mendatang)</h5>

                            <?php if ($peminjaman->num_rows === 0): ?>
                                <p class="text-muted mb-0">Tidak ada peminjaman terjadwal yang disetujui untuk ruangan ini dalam 7 hari ke depan.</p>
                            <?php else: ?>
                                <div class="table-responsive mt-2">
                                    <table class="table table-bordered align-middle mb-0">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Waktu</th>
                                                <th>Dosen</th>
                                                <th>Keperluan</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($p = $peminjaman->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($p['tanggal'] ?? '-'); ?></td>
                                                    <td><?= safe_time_short($p['waktu_mulai'] ?? ''); ?> &ndash; <?= safe_time_short($p['waktu_selesai'] ?? ''); ?></td>
                                                    <td><?= htmlspecialchars($p['nama_dosen_peminjam'] ?? '-'); ?></td>
                                                    <td><?= nl2br(htmlspecialchars($p['keperluan'] ?? '-')); ?></td>
                                                    <td>
                                                        <?php if (($p['status'] ?? '') === 'Disetujui'): ?>
                                                            <span class="badge bg-success">Disetujui</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary"><?= htmlspecialchars($p['status'] ?? '-'); ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="peminjaman_detail.php?id=<?= urlencode($p['kode_peminjaman'] ?? ''); ?>" class="btn btn-outline-primary btn-sm">Detail</a>
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

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            const kode = <?= json_encode($kode); ?>;
            const badge = document.getElementById('room-status-badge');
            const detail = document.getElementById('room-status-detail');

            function render(data) {
                if (!data) return;
                if (data.status === 'unavailable') {
                    badge.className = 'badge rounded-pill bg-danger p-2 px-3';
                    badge.textContent = 'Tidak tersedia';
                        if (data.current) {
                            if (data.current.tanggal) {
                                if (detail) detail.textContent = (data.current.tanggal || '') + ' ' + (data.current.waktu_mulai || '') + '–' + (data.current.waktu_selesai || '');
                            } else if (data.current.hari) {
                                if (detail) detail.textContent = (data.current.hari || '') + ' ' + (data.current.jam_mulai || '') + '–' + (data.current.jam_selesai || '');
                            } else {
                                if (detail) detail.textContent = '';
                            }
                        } else {
                            if (detail) detail.textContent = '';
                        }
                } else {
                    badge.className = 'badge rounded-pill bg-success p-2 px-3';
                    badge.textContent = 'Tersedia';
                    if (data.next) {
                        if (detail) detail.textContent = 'Next: ' + data.next.tanggal + ' ' + (data.next.waktu_mulai || '');
                    } else {
                        if (detail) detail.textContent = 'Tidak ada booking mendatang.';
                    }
                }
            }

            function fetchStatus() {
                const now = new Date();
                const localDate = now.toISOString().slice(0, 10);
                const hh = String(now.getHours()).padStart(2, '0');
                const mm = String(now.getMinutes()).padStart(2, '0');
                const ss = String(now.getSeconds()).padStart(2, '0');
                const localTime = hh + ':' + mm + ':' + ss;
                const url = '../backend/room_status.php?kode=' + encodeURIComponent(kode) + '&local_date=' + encodeURIComponent(localDate) + '&local_time=' + encodeURIComponent(localTime);

                fetch(url)
                    .then(r => r.json()).then(render).catch(e => {
                        badge.className = 'badge bg-secondary';
                        badge.textContent = 'Status tidak tersedia';
                        if (detail) detail.textContent = '';
                    });
            }

            fetchStatus();
            setInterval(fetchStatus, 300000); // 5 minutes
        })();
    </script>
</body>

</html>