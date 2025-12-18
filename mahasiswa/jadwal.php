<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

require_login();
if (!is_mahasiswa()) {
    header('Location: login.php');
    exit;
}

$user = current_user();

// AMBIL JADWAL GLOBAL (yang diinput admin)
// TANPA KRS — karena database kamu tidak punya tabel tersebut
$stmt = $conn->prepare("
    SELECT 
        j.*,
        mk.nama_mk,
        d.nama_dosen,
        r.nama_ruang
    FROM jadwal j
    LEFT JOIN matakuliah mk ON j.kode_mk    = mk.kode_mk
    LEFT JOIN dosen      d  ON j.id_dosen   = d.id_dosen
    LEFT JOIN ruang      r  ON j.kode_ruang = r.kode_ruang
    ORDER BY 
        FIELD(j.hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'),
        j.jam_mulai
");
$stmt->execute();
$jadwal = $stmt->get_result();
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Jadwal Kuliah Mahasiswa - EasyRoom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container py-4">

        <h3 class="fw-bold text-primary mb-3">
            <i class="bi bi-calendar-week me-2"></i> Jadwal Kuliah
        </h3>
        <p class="text-muted">
            Berikut jadwal kuliah yang sudah diinput oleh admin. Mahasiswa hanya dapat melihat jadwal ini.
        </p>

        <div class="card shadow-sm">
            <div class="card-body">

                <?php if ($jadwal->num_rows == 0): ?>
                    <p class="text-muted">Belum ada jadwal kuliah.</p>
                <?php else: ?>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th>Hari</th>
                                    <th>Waktu</th>
                                    <th>Mata Kuliah</th>
                                    <th>Dosen</th>
                                    <th>Ruangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $maxDt = new DateTime('+6 days');
                                while ($row = $jadwal->fetch_assoc()):
                                    $nextDate = get_next_date_for_day($row['hari'] ?? '');
                                    $nextDt = new DateTime($nextDate);
                                    // hanya tampilkan yang jatuh dalam rentang 7 hari (hari ini..+6)
                                    if ($nextDt > $maxDt) continue;
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['hari'] ?? '-'); ?></td>
                                        <td>
                                            <?php
                                            if (!empty($row['jam_mulai']) && !empty($row['jam_selesai'])) {
                                                echo safe_time_short($row['jam_mulai']) . ' - ' . safe_time_short($row['jam_selesai']);
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                        <td><?= htmlspecialchars($row['nama_mk'] ?? '-'); ?></td>
                                        <td><?= htmlspecialchars($row['nama_dosen'] ?? '-'); ?></td>
                                        <td><?= htmlspecialchars($row['nama_ruang'] ?? '-'); ?></td>
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