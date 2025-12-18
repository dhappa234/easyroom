<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';
require_login();
if (!is_admin()) {
    header('Location: login.php');
    exit;
}

$role = $_GET['role'] ?? '';

if (!in_array($role, ['admin', 'dosen', 'mahasiswa'])) {
    die("Role tidak ditemukan.");
}

// Fungsi generate ID otomatis: Prefix + 3 Huruf Acak
function generate_auto_id($role)
{
    $prefix = ($role === 'admin') ? 'ADM' : (($role === 'dosen') ? 'DSN' : 'MHS');
    $random_chars = substr(str_shuffle('1234567890'), 0, 3);
    return $prefix . $random_chars;
}
$auto_id = generate_auto_id($role);
?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Tambah User - <?= ucfirst($role); ?></title>

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

            <h3 class="fw-bold text-primary mb-3">Tambah <?= ucfirst($role); ?></h3>

            <div class="card p-4 shadow-sm">

                <form action="../backend/admin/user_action.php" method="post">

                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="role" value="<?= $role; ?>">

                    <?php if ($role == 'admin'): ?>

                        <div class="mb-3">
                            <label>ID Admin (Otomatis)</label>
                            <input type="text" name="id_admin" class="form-control bg-light" value="<?= $auto_id; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Nama Admin</label>
                            <input type="text" name="nama_admin" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                    <?php elseif ($role == 'dosen'): ?>

                        <div class="mb-3">
                            <label>ID Dosen (Otomatis)</label>
                            <input type="text" name="id_dosen" class="form-control bg-light" value="<?= $auto_id; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Nama Dosen</label>
                            <input type="text" name="nama_dosen" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>NIP</label>
                            <input type="text" name="nip" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                    <?php else: ?>

                        <div class="mb-3">
                            <label>ID Mahasiswa (Otomatis)</label>
                            <input type="text" name="id_mahasiswa" class="form-control bg-light" value="<?= $auto_id; ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Nama Mahasiswa</label>
                            <input type="text" name="nama_mahasiswa" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>NIM</label>
                            <input type="text" name="nim" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Jurusan</label>
                            <input type="text" name="jurusan" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                    <?php endif; ?>

                    <div class="mb-3">
                        <label>Password</label>
                        <input type="text" name="password" class="form-control" required>
                    </div>
                    <a href="users.php" class="btn btn-outline-secondary">Kembali</a>
                    <button class="btn btn-primary">Simpan</button>

                </form>

            </div>

        </div>

    </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>