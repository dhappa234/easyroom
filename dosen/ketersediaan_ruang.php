<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';
require_login();
if (!is_dosen()) {
    header('Location: login.php');
    exit;
}

// hit semua ruang
$ruang = $conn->query("SELECT * FROM ruang ORDER BY nama_ruang");

// siapkan nama hari untuk 7 hari ke depan (Indonesia)
$map = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
$nextDays = [];
for ($i = 0; $i < 7; $i++) {
    $n = (int)date('N', strtotime("+{$i} days"));
    $nextDays[] = $map[$n];
}
$nextDays = array_unique($nextDays);
$daysIn = "'" . implode("','", $nextDays) . "'";
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Ketersediaan Ruang - Dosen</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body class="mhs-page">

    <?php include 'navbar.php'; ?>

    <div class="container mhs-container">

        <div class="page-header d-flex align-items-center justify-content-between">
            <div>
                <h3 class="page-header-title mb-1">
                    <i class="bi bi-door-open me-2"></i>Ketersediaan Ruang
                </h3>
                <p class="page-header-subtitle mb-0">
                    Lihat daftar ruangan beserta kapasitas dan status penggunaannya.
                </p>
            </div>
        </div>

        <div class="row g-4">
            <?php while ($r = $ruang->fetch_assoc()):
                // hitung jumlah jadwal pada 7 hari ke depan (berdasarkan nama hari)
                $kode = $conn->real_escape_string($r['kode_ruang']);
                $sqlJ = "SELECT COUNT(*) FROM jadwal WHERE kode_ruang = '{$kode}' AND hari IN ({$daysIn})";
                $jadwal_count_next7 = (int)$conn->query($sqlJ)->fetch_row()[0];

                // hitung peminjaman disetujui 7 hari ke depan
                $stmt = $conn->prepare("SELECT COUNT(*) FROM peminjaman WHERE kode_ruang = ? AND status = 'Disetujui' AND tanggal BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 6 DAY)");
                $stmt->bind_param("s", $r['kode_ruang']);
                $stmt->execute();
                $stmt->bind_result($approved_count);
                $stmt->fetch();
                $stmt->close();

                // tanggal peminjaman berikutnya (jika ada)
                $stmt2 = $conn->prepare("SELECT MIN(tanggal) FROM peminjaman WHERE kode_ruang = ? AND status = 'Disetujui' AND tanggal >= CURDATE()");
                $stmt2->bind_param("s", $r['kode_ruang']);
                $stmt2->execute();
                $stmt2->bind_result($next_booking_date);
                $stmt2->fetch();
                $stmt2->close();

                $tersedia = ($jadwal_count_next7 === 0 && (int)$approved_count === 0);
            ?>
                <div class="col-md-4">
                    <div class="hover-card p-4 h-100 d-flex flex-column">

                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="fw-bold text-primary mb-1"><?= htmlspecialchars($r['nama_ruang']); ?></h5>
                                <p class="text-muted small mb-1">Kapasitas: <?= (int)$r['kapasitas']; ?> orang</p>
                            </div>
                            <span class="badge bg-secondary" id="room-status-<?= htmlspecialchars($r['kode_ruang']); ?>">Memuat...</span>
                        </div>

                        

                        <div class="mt-3 d-flex gap-2">
                            <a href="ruang_detail.php?kode=<?= urlencode($r['kode_ruang']); ?>" class="btn btn-outline-primary btn-sm">Lihat Detail Ruangan</a>
                            <a href="pengajuan_step1.php?ruang=<?= urlencode($r['kode_ruang']); ?>" class="btn btn-primary btn-sm">Ajukan Peminjaman</a>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            const badgeEls = Array.from(document.querySelectorAll('[id^="room-status-"]'));

            function refreshAll() {
                if (badgeEls.length === 0) return;
                const now = new Date();
                const localDate = now.toISOString().slice(0, 10);
                const hh = String(now.getHours()).padStart(2, '0');
                const mm = String(now.getMinutes()).padStart(2, '0');
                const ss = String(now.getSeconds()).padStart(2, '0');
                const localTime = hh + ':' + mm + ':' + ss;

                const params = new URLSearchParams();
                badgeEls.forEach(b => params.append('kode[]', b.id.replace('room-status-', '')));
                params.append('local_date', localDate);
                params.append('local_time', localTime);

                const url = '../backend/room_status_batch.php?' + params.toString();

                fetch(url).then(r => r.json()).then(data => {
                    // data: { kode: { ... } }
                    badgeEls.forEach(b => {
                        const kode = b.id.replace('room-status-', '');
                        const detailEl = document.getElementById('room-status-detail-' + kode);
                        const item = data[kode];
                        if (!item) {
                            b.className = 'badge bg-secondary';
                            b.textContent = 'Status tidak tersedia';
                            if (detailEl) detailEl.textContent = '';
                            return;
                        }

                        if (item.status === 'unavailable') {
                            b.className = 'badge bg-danger';
                            b.textContent = 'Tidak tersedia';
                            if (detailEl) {
                                if (item.current && item.current.tanggal) {
                                    detailEl.innerHTML = (item.current.tanggal || '') + ' ' + (item.current.waktu_mulai || '') + '–' + (item.current.waktu_selesai || '');
                                } else if (item.current && item.current.hari) {
                                    detailEl.innerHTML = (item.current.hari || '') + ' ' + (item.current.jam_mulai || '') + '–' + (item.current.jam_selesai || '');
                                } else if (item.next) {
                                    detailEl.textContent = 'Next: ' + item.next.tanggal + ' ' + (item.next.waktu_mulai || '');
                                } else {
                                    detailEl.textContent = '';
                                }
                            }
                        } else {
                            b.className = 'badge bg-success';
                            b.textContent = 'Tersedia';
                            if (detailEl) {
                                if (item.next) {
                                    detailEl.textContent = 'Next: ' + item.next.tanggal + ' ' + (item.next.waktu_mulai || '');
                                } else {
                                    detailEl.textContent = 'Ruangan kosong dalam 7 hari ke depan.';
                                }
                            }
                        }
                    });
                }).catch(err => {
                    badgeEls.forEach(b => {
                        const kode = b.id.replace('room-status-', '');
                        const detailEl = document.getElementById('room-status-detail-' + kode);
                        b.className = 'badge bg-secondary';
                        b.textContent = 'Status tidak tersedia';
                        if (detailEl) detailEl.textContent = '';
                    });
                });
            }

            // initial
            refreshAll();
            // refresh every 60 seconds
            setInterval(refreshAll, 60000);
        })();
    </script>
</body>

</html>