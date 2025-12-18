<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';
require_login();
if (!is_admin()) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? '';
if ($id === '') {
    header('Location: peminjaman.php');
    exit;
}

$stmt = $conn->prepare("
    SELECT p.*, r.nama_ruang, d.nama_dosen, d.email 
    FROM peminjaman p
    JOIN ruang r ON p.kode_ruang = r.kode_ruang
    JOIN dosen d ON p.id_dosen = d.id_dosen
    WHERE p.kode_peminjaman = ?
");
$stmt->bind_param("s", $id);
$stmt->execute();
$detail = $stmt->get_result()->fetch_assoc();

if (!$detail) {
    die("Pengajuan tidak ditemukan.");
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Detail Peminjaman - Admin EasyRoom</title>

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
                <i class="bi bi-journal-text me-2"></i> Detail Pengajuan Peminjaman
            </h3>

            <div class="row g-4">

                <!-- Detail -->
                <div class="col-md-7">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">

                            <h5 class="fw-semibold mb-3 text-primary">
                                Pengajuan #<?= $detail['kode_peminjaman']; ?>
                            </h5>

                            <table class="table table-sm">
                                <tr>
                                    <th style="width:35%;">Dosen</th>
                                    <td><?= $detail['nama_dosen']; ?> (<?= $detail['email']; ?>)</td>
                                </tr>
                                <tr>
                                    <th>Ruang</th>
                                    <td><?= $detail['nama_ruang']; ?> (<?= $detail['kode_ruang']; ?>)</td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td><?= $detail['tanggal']; ?></td>
                                </tr>
                                <tr>
                                    <th>Waktu</th>
                                    <td><?= $detail['waktu_mulai']; ?> - <?= $detail['waktu_selesai']; ?></td>
                                </tr>
                                <tr>
                                    <th>Keperluan</th>
                                    <td><?= nl2br(htmlspecialchars($detail['keperluan'])); ?></td>
                                </tr>
                                <tr>
                                    <th>File Pengajuan</th>
                                    <td>
                                        <?php if (!empty($detail['file_pengajuan'])): ?>
                                            <a href="/<?= $detail['file_pengajuan']; ?>" target="_blank">Lihat File</a>
                                        <?php else: ?>
                                            <span class="text-muted">Tidak ada file lampiran.</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <?php if ($detail['status'] === 'Menunggu'): ?>
                                            <span class="badge bg-warning text-dark">Menunggu</span>
                                        <?php elseif ($detail['status'] === 'Disetujui'): ?>
                                            <span class="badge bg-success">Disetujui</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Ditolak</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <?php if (!empty($detail['catatan_admin'])): ?>
                                    <tr>
                                        <th>Catatan Admin</th>
                                        <td><?= nl2br(htmlspecialchars($detail['catatan_admin'])); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </table>

                            <a href="peminjaman.php" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>

                        </div>
                    </div>
                </div>

                <!-- Persetujuan -->
                <div class="col-md-5">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">

                            <h6 class="fw-semibold text-primary mb-3">Tindakan Admin</h6>

                            <?php if ($detail['status'] !== 'Menunggu'): ?>
                                <div class="alert alert-info py-2">
                                    Pengajuan sudah memiliki status: <strong><?= $detail['status']; ?></strong><br>
                                    Anda tetap bisa memperbaruinya.
                                </div>
                            <?php endif; ?>

                            <form action="../backend/admin/peminjaman_action.php" method="post">
                                <input type="hidden" name="kode_peminjaman" value="<?= $detail['kode_peminjaman']; ?>">

                                <div class="mb-3">
                                    <label class="form-label">Catatan Admin (opsional)</label>
                                    <textarea name="catatan_admin" class="form-control" rows="3"
                                        placeholder="Contoh: Disetujui dengan ketentuan tepat waktu."><?= htmlspecialchars($detail['catatan_admin'] ?? ""); ?></textarea>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" name="aksi" value="setujui" class="btn btn-success">
                                        <i class="bi bi-check-circle me-1"></i> Setujui
                                    </button>
                                    <button type="submit" name="aksi" value="tolak" class="btn btn-danger">
                                        <i class="bi bi-x-circle me-1"></i> Tolak
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>