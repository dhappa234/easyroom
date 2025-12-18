<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';
require_login();
if (!is_admin()) {
    header('Location: login.php');
    exit;
}

$user = current_user();
$id_admin = $user['id'];

$error = '';
$success = '';

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password_baru = trim($_POST['password_baru']);

    if ($password_baru === '') {
        $error = "Password baru tidak boleh kosong.";
    } else {
        $stmt = $conn->prepare("UPDATE admin SET password = ? WHERE id_admin = ?");
        // KOREKSI: Mengubah "si" menjadi "ss"
        // 's' untuk $password_baru, dan 's' untuk $id_admin (yang berupa string)
        $stmt->bind_param("ss", $password_baru, $id_admin); 
        $stmt->execute(); // Baris 25, sekarang harusnya berhasil

        $success = "Password berhasil diperbarui!";
    }
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Profil Admin - EasyRoom</title>

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
                <i class="bi bi-person-circle me-2"></i> Profil Admin
            </h3>

            <div class="row g-4">

                <div class="col-md-5">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3 text-primary"><?= htmlspecialchars($user['nama']); ?></h5>

                            <p class="text-muted mb-1">
                                <i class="bi bi-person-badge me-2 text-primary"></i>
                                <strong>Username:</strong> <?= htmlspecialchars($user['username']); ?>
                            </p>

                            <p class="text-muted mb-1">
                                <i class="bi bi-shield-lock me-2 text-primary"></i>
                                <strong>Role:</strong> Administrator
                            </p>

                            <p class="small text-muted mt-3">
                                Akun admin dapat mengelola semua data dalam sistem.
                            </p>
                        </div>
                    </div>
                </div>

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
                                    <input type="password" name="password_baru" class="form-control" required>
                                </div>

                                <button class="btn btn-primary btn-anim" type="submit">
                                    <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
                                </button>
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