<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';
require_login();
if (!is_dosen()) {
    header('Location: login.php');
    exit;
}

$user = current_user();
$id_dosen = $user['id'];

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password_baru = trim($_POST['password_baru'] ?? '');

    if ($password_baru === '') {
        $error = "Password baru tidak boleh kosong.";
    } else {
        $stmt = $conn->prepare("UPDATE dosen SET password = ? WHERE id_dosen = ?");
        $stmt->bind_param("si", $password_baru, $id_dosen);
        $stmt->execute();
        $success = "Password berhasil diperbarui.";
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Profil Dosen - EasyRoom</title>

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
            <i class="bi bi-person-circle me-2"></i>Profil Dosen
        </h3>
        <p class="page-header-subtitle mb-0">
            Lihat informasi akun dan ganti password Anda secara berkala.
        </p>
    </div>

    <div class="row g-4">

        <!-- Info Dosen -->
        <div class="col-md-5">
            <div class="hover-card p-4">
                <h5 class="fw-bold text-primary mb-3">
                    <?= htmlspecialchars($user['nama']); ?>
                </h5>

                <?php if (isset($user['username'])): ?>
                    <p class="mb-1"><strong>Email / Username:</strong>
                        <?= htmlspecialchars($user['username']); ?>
                    </p>
                <?php endif; ?>

                <p class="small text-muted mt-3 mb-0">
                    Jika terdapat kesalahan data, silakan hubungi admin fakultas.
                </p>
            </div>
        </div>

        <!-- Ganti Password -->
        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h5 class="fw-semibold text-primary mb-3">Ganti Password</h5>

                    <?php if ($success): ?>
                        <div class="alert alert-success py-2"><?= $success; ?></div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2"><?= $error; ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password_baru"
                                   class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-anim">
                            <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
                        </button>
                    </form>

                    <p class="small text-muted mt-3 mb-0">
                        Gunakan password yang kuat dan jangan membagikannya kepada pihak lain.
                    </p>

                </div>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
