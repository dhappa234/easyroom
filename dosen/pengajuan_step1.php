<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';
require_login();
if (!is_dosen()) {
    header('Location: login.php');
    exit;
}

$user = current_user();

$kode_ruang = $_GET['ruang'] ?? '';
$ruang = null;

if ($kode_ruang) {
    $stmt = $conn->prepare("SELECT * FROM ruang WHERE kode_ruang = ?");
    $stmt->bind_param("s", $kode_ruang);
    $stmt->execute();
    $ruang = $stmt->get_result()->fetch_assoc();
}

$minDate = date('Y-m-d');
$maxDate = date('Y-m-d', strtotime('+3 days'));
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Ajukan Peminjaman - Tahap 1</title>

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
                <i class="bi bi-file-earmark-plus me-2"></i>Ajukan Peminjaman (Tahap 1)
            </h3>
            <p class="page-header-subtitle mb-0">
                Isi informasi dasar untuk peminjaman ruangan, kemudian lanjut ke tahap konfirmasi.
            </p>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">

                <form action="../backend/admin/pengajuan_step1_process.php" method="post" enctype="multipart/form-data">

                    <div class="row g-4">

                        <div class="col-md-6">
                            <label class="form-label">Ruang</label>
                            <input type="text" class="form-control"
                                value="<?= $ruang ? htmlspecialchars($ruang['nama_ruang']) : 'Pilih dari halaman ketersediaan'; ?>"
                                readonly>
                            <input type="hidden" name="kode_ruang"
                                value="<?= htmlspecialchars($kode_ruang); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tanggal</label>
                            <?php $minDate = date('Y-m-d');
                            $maxDate = date('Y-m-d', strtotime('+3 days')); ?>
                            <input type="date" name="tanggal" class="form-control" min="<?= $minDate ?>" max="<?= $maxDate ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Waktu Mulai</label>
                            <input type="time" name="waktu_mulai" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Waktu Selesai</label>
                            <input type="time" name="waktu_selesai" class="form-control" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Keperluan</label>
                            <textarea name="keperluan" class="form-control"
                                rows="3" required></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">
                                Upload Surat Pengajuan (PDF/JPG, opsional)
                            </label>
                            <input type="file" name="file_pengajuan" class="form-control">
                        </div>

                    </div>

                    <div class="mt-4">
                        <a href="ketersediaan_ruang.php"
                            class="btn btn-outline-secondary">
                            Kembali
                        </a>
                        <button type="submit" class="btn btn-primary btn-anim">
                            Lanjut ke Konfirmasi
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>