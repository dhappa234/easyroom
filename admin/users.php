<?php
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';
require_login();
if (!is_admin()) {
    header("Location: login.php");
    exit;
}

// ambil data
$admin = $conn->query("SELECT * FROM admin ORDER BY nama_admin");
$dosen = $conn->query("SELECT * FROM dosen ORDER BY nama_dosen");
$mahasiswa = $conn->query("SELECT * FROM mahasiswa ORDER BY nama_mahasiswa");
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Kelola Users - Admin</title>

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

            <h3 class="fw-bold text-primary mb-3"><i class="bi bi-people me-2"></i>Kelola Users</h3>

            <!-- Tab Menu -->
            <ul class="nav nav-tabs" id="userTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#adminTab">Admin</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#dosenTab">Dosen</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#mhsTab">Mahasiswa</button>
                </li>
            </ul>
            <div class="card">
                <div class="card-body">

                <div class="tab-content mt-4">

                    <!-- ADMIN TAB -->
                    <div class="tab-pane fade show active" id="adminTab">

                        <div class="d-flex justify-content-between mb-2">
                            <h5 class="fw-semibold">Daftar Admin</h5>
                            <a href="user_add.php?role=admin" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Admin
                            </a>
                        </div>

                        <div class="table-responsive mt-3">
                            <table class="table table-bordered align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Username</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($a = $admin->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $a['nama_admin']; ?></td>
                                            <td><?= $a['username']; ?></td>
                                            <td>
                                                <a href="user_edit.php?role=admin&id=<?= $a['id_admin']; ?>"
                                                    class="btn btn-warning btn-sm">
                                                    Edit
                                                </a>
                                                <a href="/backend/admin/user_action.php?role=admin&delete=<?= $a['id_admin']; ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Hapus admin ini?');">
                                                    Hapus
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <!-- DOSEN TAB -->
                    <div class="tab-pane fade" id="dosenTab">

                        <div class="d-flex justify-content-between mb-2">
                            <h5 class="fw-semibold">Daftar Dosen</h5>
                            <a href="user_add.php?role=dosen" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Dosen
                            </a>
                        </div>

                        <div class="table-responsive mt-3">
                            <table class="table table-bordered align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>NIP</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($d = $dosen->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $d['nama_dosen']; ?></td>
                                            <td><?= $d['email']; ?></td>
                                            <td><?= $d['nip']; ?></td>
                                            <td>
                                                <a href="user_edit.php?role=dosen&id=<?= $d['id_dosen']; ?>"
                                                    class="btn btn-warning btn-sm">Edit</a>
                                                <a href="../backend/admin/user_action.php?role=dosen&delete=<?= $d['id_dosen']; ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Hapus dosen ini?');">
                                                    Hapus
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <!-- MAHASISWA TAB -->
                    <div class="tab-pane fade" id="mhsTab">

                        <div class="d-flex justify-content-between mb-2">
                            <h5 class="fw-semibold">Daftar Mahasiswa</h5>
                            <a href="user_add.php?role=mahasiswa" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Mhs
                            </a>
                        </div>

                        <div class="table-responsive mt-3">
                            <table class="table table-bordered align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>NIM</th>
                                        <th>Jurusan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($m = $mahasiswa->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $m['nama_mahasiswa']; ?></td>
                                            <td><?= $m['email']; ?></td>
                                            <td><?= $m['nim']; ?></td>
                                            <td><?= $m['jurusan']; ?></td>
                                            <td>
                                                <a href="user_edit.php?role=mahasiswa&id=<?= $m['id_mahasiswa']; ?>"
                                                    class="btn btn-warning btn-sm">Edit</a>
                                                <a href="../backend/admin/user_action.php?role=mahasiswa&delete=<?= $m['id_mahasiswa']; ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Hapus mahasiswa ini?');">
                                                    Hapus
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>