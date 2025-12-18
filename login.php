<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// Pastikan session aktif (kalau belum dijalankan di config.php / functions.php)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika sudah login, langsung ke dashboard sesuai role
if (is_logged_in()) {
    if (is_admin()) {
        header('Location: admin/index.php');
    } elseif (is_dosen()) {
        header('Location: dosen/dashboard.php');
    } elseif (is_mahasiswa()) {
        header('Location: mahasiswa/index.php');
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Username/NIM/email dan password wajib diisi.';
    } else {

        // ------------ LOGIN ADMIN ------------
        $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ? AND password = ?");
        $stmt->bind_param('ss', $username, $password);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($admin = $res->fetch_assoc()) {
            session_regenerate_id(true);

            $_SESSION['user'] = [
                'role'     => 'admin',
                'id'       => $admin['id_admin'],
                'nama'     => $admin['nama_admin'],
                'username' => $admin['username']
            ];

            header('Location: admin/index.php');
            exit;
        }
        $stmt->close();

        // ------------ LOGIN DOSEN (NIP) ------------
        $stmt = $conn->prepare("SELECT * FROM dosen WHERE nip = ? AND password = ?");
        $stmt->bind_param('ss', $username, $password);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($dosen = $res->fetch_assoc()) {
            session_regenerate_id(true);

            $_SESSION['user'] = [
                'role'     => 'dosen',
                'id'       => $dosen['id_dosen'],
                'nama'     => $dosen['nama_dosen'],
                'username' => $dosen['nip']
            ];

            header('Location: dosen/dashboard.php');
            exit;
        }
        $stmt->close();

        // ------------ LOGIN MAHASISWA (NIM / EMAIL) ------------
        $stmt = $conn->prepare("
            SELECT * FROM mahasiswa 
            WHERE (nim = ? OR email = ?) AND password = ?
        ");
        $stmt->bind_param('sss', $username, $username, $password);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($mhs = $res->fetch_assoc()) {
            session_regenerate_id(true);

            $_SESSION['user'] = [
                'role'     => 'mahasiswa',
                'id'       => $mhs['id_mahasiswa'],
                'nama'     => $mhs['nama_mahasiswa'],
                'username' => $mhs['nim']   
            ];

            // SESUAIKAN: arahkan ke index.php mahasiswa
            header('Location: mahasiswa/index.php');
            exit;
        }
        $stmt->close();

        // ------------ LOGIN GAGAL ------------
        $error = 'Login gagal. Periksa kembali data Anda.';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Login - EasyRoom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="auth-page">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5 col-lg-4">
        <div class="auth-card p-4">
          <div class="text-center mb-3">
            <div class="auth-brand mb-1">EasyRoom</div>
            <small class="text-muted">Sistem Manajemen Ruangan & Jadwal Perkuliahan</small>
          </div>

          <h3 class="text-center mb-2 text-dark">Login</h3>

          <?php if ($error): ?>
            <div class="alert alert-danger py-2"><?= htmlspecialchars($error); ?></div>
          <?php endif; ?>

          <form method="post">
            <div class="mb-3">
              <label class="form-label text-muted">Username</label>
              <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
              <label class="form-label text-muted">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100 btn-anim" type="submit">
              <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
            </button>
          </form>

          <div class="mt-3 text-center">
            <a href="index.php" class="small text-primary">
              <i class="bi bi-arrow-left me-1"></i> Kembali ke Landing Page
            </a>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
