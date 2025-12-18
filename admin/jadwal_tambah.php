<?php
// admin/jadwal_tambah.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

require_login();
if (!is_admin()) {
    header('Location: login.php');
    exit;
}

// Ambil pilihan matakuliah, dosen, dan ruang
$matakuliah = $conn->query("SELECT kode_mk, nama_mk FROM matakuliah ORDER BY nama_mk");
$dosen      = $conn->query("SELECT id_dosen, nama_dosen FROM dosen ORDER BY nama_dosen");
$ruang      = $conn->query("SELECT kode_ruang, nama_ruang FROM ruang ORDER BY nama_ruang");

$error_message = '';
// If kode is provided, load existing jadwal for edit
$editing = false;
$existing = null;
if (isset($_GET['kode']) && $_GET['kode'] !== '') {
    $kode_edit = $_GET['kode'];
    $stmt = $conn->prepare("SELECT * FROM jadwal WHERE kode_jadwal = ?");
    $stmt->bind_param("s", $kode_edit);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($existing) $editing = true;
}

if (isset($_GET['error'])) {
    $error_code = $_GET['error'];
    if ($error_code === 'incomplete') {
        $error_message = 'Gagal menyimpan. Harap lengkapi semua data yang diperlukan.';
    } elseif ($error_code === 'conflict_jadwal') {
        $error_message = 'Gagal menyimpan. Ruangan ini sudah digunakan untuk jadwal kuliah lain pada hari dan jam tersebut.';
    } elseif ($error_code === 'conflict_peminjaman') {
        $error_message = 'Gagal menyimpan. Ruangan ini sedang dipinjam pada hari dan jam tersebut.';
    } else {
        $error_message = 'Terjadi kesalahan saat menyimpan data.';
    }
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Tambah Jadwal - Admin | EasyRoom</title>
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

            <div class="mb-3">
                <h3 class="fw-bold text-primary mb-1">
                    <i class="bi bi-plus-circle me-2"></i> <?= $editing ? 'Edit Jadwal Kuliah' : 'Tambah Jadwal Kuliah'; ?>
                </h3>
                <p class="text-muted mb-0">
                    <?= $editing ? 'Ubah data jadwal berikut, lalu simpan.' : 'Lengkapi data berikut untuk menambahkan jadwal perkuliahan baru.'; ?>
                </p>
            </div>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger py-2" role="alert">
                    <?= htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <form action="../backend/admin/jadwal_action.php" method="post">
                        <input type="hidden" name="action" value="<?= $editing ? 'update' : 'create'; ?>">
                        <?php if ($editing): ?>
                            <input type="hidden" name="kode_jadwal" value="<?= htmlspecialchars($existing['kode_jadwal']); ?>">
                        <?php endif; ?>

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Mata Kuliah</label>
                                <select name="kode_mk" class="form-select" required>
                                    <option value="">-- Pilih Mata Kuliah --</option>
                                    <?php while ($mk = $matakuliah->fetch_assoc()): ?>
                                        <option value="<?= htmlspecialchars($mk['kode_mk']); ?>" <?= ($editing && ($existing['kode_mk'] ?? '') === $mk['kode_mk']) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($mk['nama_mk']); ?> (<?= htmlspecialchars($mk['kode_mk']); ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Dosen Pengampu</label>
                                <select name="id_dosen" class="form-select" required>
                                    <option value="">-- Pilih Dosen --</option>
                                    <?php while ($d = $dosen->fetch_assoc()): ?>
                                        <option value="<?= htmlspecialchars($d['id_dosen']); ?>" <?= ($editing && ($existing['id_dosen'] ?? '') == $d['id_dosen']) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($d['nama_dosen']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Ruangan</label>
                                <select name="kode_ruang" class="form-select" required>
                                    <option value="">-- Pilih Ruangan --</option>
                                    <?php while ($r = $ruang->fetch_assoc()): ?>
                                        <option value="<?= htmlspecialchars($r['kode_ruang']); ?>" <?= ($editing && ($existing['kode_ruang'] ?? '') === $r['kode_ruang']) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($r['nama_ruang']); ?> (<?= htmlspecialchars($r['kode_ruang']); ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Kelas</label>
                                <input type="text" name="kelas" class="form-control" placeholder="misal: TI-1A" required value="<?= $editing ? htmlspecialchars($existing['kelas']) : ''; ?>">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Hari</label>
                                <select name="hari" class="form-select" required>
                                    <option value="">-- Pilih Hari --</option>
                                    <?php foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h): ?>
                                        <option <?= ($editing && ($existing['hari'] ?? '') === $h) ? 'selected' : ''; ?>><?= $h ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Jam Mulai</label>
                                <input type="time" name="jam_mulai" class="form-control" required value="<?= $editing ? substr($existing['jam_mulai'],0,5) : ''; ?>">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Jam Selesai</label>
                                <input type="time" name="jam_selesai" class="form-control" required value="<?= $editing ? substr($existing['jam_selesai'],0,5) : ''; ?>">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Semester</label>
                                <input type="text" name="semester" class="form-control" placeholder="isi dengan angka" required value="<?= $editing ? htmlspecialchars($existing['semester']) : ''; ?>">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Kuota Peserta</label>
                                <input type="number" name="kuota_peserta" class="form-control" min="0" value="<?= $editing ? (int)$existing['kuota_peserta'] : 0; ?>">
                            </div>

                        </div>

                        <div class="mt-4 d-flex justify-content-between">
                            <a href="jadwal.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary btn-anim">
                                <i class="bi bi-check2-circle me-1"></i> <?= $editing ? 'Perbarui Jadwal' : 'Simpan Jadwal'; ?>
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>