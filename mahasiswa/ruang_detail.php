<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

require_login();
if (!is_mahasiswa()) {
    header('Location: login.php');
    exit;
}

$kode_ruang = $_GET['kode'] ?? '';
if ($kode_ruang === '') {
    header('Location: ketersediaan.php');
    exit;
}

// 1. Ambil data ruangan
$stmt = $conn->prepare("SELECT * FROM ruang WHERE kode_ruang = ?");
$stmt->bind_param("s", $kode_ruang);
$stmt->execute();
$ruang = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$ruang) {
    die("Ruangan tidak ditemukan.");
}

// 2. Ambil jadwal penggunaan ruangan ini
$stmt2 = $conn->prepare("
    SELECT 
        j.*,
        mk.nama_mk      AS nama_matakuliah,
        d.nama_dosen    AS nama_dosen
    FROM jadwal j
    LEFT JOIN matakuliah mk ON j.kode_mk   = mk.kode_mk
    LEFT JOIN dosen      d  ON j.id_dosen  = d.id_dosen
    WHERE j.kode_ruang = ?
    ORDER BY 
        FIELD(j.hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'),
        j.jam_mulai
");
$stmt2->bind_param("s", $kode_ruang);
$stmt2->execute();
$jadwal = $stmt2->get_result();   // ← INI yang nanti kita pakai di HTML
$stmt2->close();

// 3. Ambil peminjaman yang telah disetujui untuk ruangan ini (hari ini sampai +6 hari = 7 hari)
$stmt3 = $conn->prepare("
        SELECT p.*, d.nama_dosen as nama_dosen_peminjam
        FROM peminjaman p
        LEFT JOIN dosen d ON p.id_dosen = d.id_dosen
        WHERE p.kode_ruang = ?
            AND p.status = 'Disetujui'
            AND p.tanggal BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 6 DAY)
        ORDER BY p.tanggal, p.waktu_mulai
");
$stmt3->bind_param("s", $kode_ruang);
$stmt3->execute();
$peminjaman = $stmt3->get_result();
$stmt3->close();
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Detail Ruangan - Mahasiswa | EasyRoom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body class="mhs-page">

    <?php include 'navbar.php'; ?>

    <div class="container mhs-container">

        <!-- Header -->
        <div class="page-header">
            <h3 class="page-header-title mb-1">
                <i class="bi bi-door-open me-2"></i> Detail Ruangan
            </h3>
            <p class="page-header-subtitle mb-0">
                Informasi ruangan dan jadwal perkuliahan yang menggunakan ruangan ini.
            </p>
            <div class="mt-2">
                <span id="room-status-badge" class="badge bg-secondary">Memuat status...</span>
            </div>
        </div>

        <div class="row g-4">

            <!-- Info Ruangan -->
            <div class="col-md-4">
                <div class="hover-card p-4">
                    <h5 class="fw-bold text-primary mb-3">
                        <?= htmlspecialchars($ruang['nama_ruang']); ?>
                    </h5>

                    <p class="mb-1">
                        <strong>Kode Ruang:</strong><br>
                        <?= htmlspecialchars($ruang['kode_ruang']); ?>
                    </p>
                    <p class="mb-1">
                        <strong>Kapasitas:</strong><br>
                        <?= (int)$ruang['kapasitas']; ?> orang
                    </p>

                    <a href="ketersediaan.php" class="btn btn-outline-secondary btn-sm mt-3">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke daftar ruangan
                    </a>
                </div>
            </div>

            <!-- Jadwal Penggunaan Ruangan -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body">

                        <h5 class="fw-semibold text-primary mb-3">
                            Jadwal Penggunaan Ruangan
                        </h5>

                        <?php if ($jadwal->num_rows === 0): ?>
                            <p class="text-muted mb-0">
                                Belum ada jadwal perkuliahan yang tercatat menggunakan ruangan ini.
                            </p>
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
                                                <td>
                                                    <?= safe_time_short($row['jam_mulai'] ?? ''); ?>
                                                    &ndash;
                                                    <?= safe_time_short($row['jam_selesai'] ?? ''); ?>
                                                </td>
                                                <td><?= htmlspecialchars($row['nama_matakuliah'] ?? '-'); ?></td>
                                                <td><?= htmlspecialchars($row['nama_dosen'] ?? '-'); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>

                        <?php endif; ?>

                        <hr>
                        <h5 class="fw-semibold text-primary mb-3">Peminjaman Disetujui (Mendatang)</h5>

                        <?php if ($peminjaman->num_rows === 0): ?>
                            <p class="text-muted mb-0">Tidak ada peminjaman terjadwal yang disetujui untuk ruangan ini dalam waktu dekat.</p>
                        <?php else: ?>
                            <div class="table-responsive mt-2">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Waktu</th>
                                            <th>Dosen</th>
                                            <th>Keperluan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($p = $peminjaman->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($p['tanggal']); ?></td>
                                                <td><?= safe_time_short($p['waktu_mulai'] ?? '') ?> &ndash; <?= safe_time_short($p['waktu_selesai'] ?? ''); ?></td>
                                                <td><?= htmlspecialchars($p['nama_dosen_peminjam'] ?? '-'); ?></td>
                                                <td><?= nl2br(htmlspecialchars($p['keperluan'] ?? '-')); ?></td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            const kode = <?= json_encode($kode_ruang); ?>;
            const badge = document.getElementById('room-status-badge');
            const detail = document.getElementById('room-status-detail');

            function render(data) {
                if (!data) return;
                if (data.status === 'unavailable') {
                    badge.className = 'badge bg-danger';
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
                    badge.className = 'badge bg-success';
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