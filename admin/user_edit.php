<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';
require_login();
if (!is_admin()) {
    header("Location: login.php");
    exit;
}

$role = $_GET['role'] ?? '';
$id   = $_GET['id'] ?? '';

// Escape string untuk keamanan dan agar support ID berupa teks
$id_safe = $conn->real_escape_string($id);

// Ambil data sesuai role
switch ($role) {
    case "admin":
        $data = $conn->query("SELECT * FROM admin WHERE id_admin = '$id_safe'")->fetch_assoc();
        break;

    case "dosen":
        $data = $conn->query("SELECT * FROM dosen WHERE id_dosen = '$id_safe'")->fetch_assoc();
        break;

    case "mahasiswa":
        $data = $conn->query("SELECT * FROM mahasiswa WHERE id_mahasiswa = '$id_safe'")->fetch_assoc();
        break;

    default:
        die("Role tidak valid.");
}
?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Edit <?= ucfirst($role); ?></title>

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

            <h3 class="fw-bold text-primary mb-3">Edit <?= ucfirst($role); ?></h3>

            <div class="card p-4 shadow-sm">

                <form action="../backend/admin/user_action.php" method="post">

                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="role" value="<?= $role; ?>">
                    <input type="hidden" name="id" value="<?= $id; ?>">

                    <?php if ($role == 'admin'): ?>

                        <div class="mb-3">
                            <label>Nama Admin</label>
                            <input type="text" name="nama_admin" class="form-control" required
                                value="<?= $data['nama_admin']; ?>">
                        </div>

                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required
                                value="<?= $data['username']; ?>">
                        </div>

                    <?php elseif ($role == 'dosen'): ?>

                        <div class="mb-3">
                            <label>Nama Dosen</label>
                            <input type="text" name="nama_dosen" class="form-control" required
                                value="<?= $data['nama_dosen']; ?>">
                        </div>

                        <div class="mb-3">
                            <label>NIP</label>
                            <input type="text" name="nip" class="form-control" required
                                value="<?= $data['nip']; ?>">
                        </div>

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required
                                value="<?= $data['email']; ?>">
                        </div>

                    <?php else: ?>

                        <div class="mb-3">
                            <label>Nama Mahasiswa</label>
                            <input type="text" name="nama_mahasiswa" class="form-control" required
                                value="<?= $data['nama_mahasiswa']; ?>">
                        </div>

                        <div class="mb-3">
                            <label>NIM</label>
                            <input type="text" name="nim" class="form-control" required
                                value="<?= $data['nim']; ?>">
                        </div>

                        <div class="mb-3">
                            <label>Jurusan</label>
                            <input type="text" name="jurusan" class="form-control" required
                                value="<?= $data['jurusan']; ?>">
                        </div>

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required
                                value="<?= $data['email']; ?>">
                        </div>

                    <?php endif; ?>

                    <div class="mb-3">
                        <label>Password <small class="text-muted">(kosongkan jika tidak diganti)</small></label>
                        <input type="text" name="password" class="form-control">
                    </div>

                    <button class="btn btn-primary">Perbarui</button>

                </form>

            </div>

        </div>

    </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>