<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../functions.php';

require_login();
if (!is_admin()) {
    header("Location: /login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {

    $kode      = trim($_POST['kode_peminjaman'] ?? '');
    $aksi      = $_POST['aksi'] ?? '';
    $catatan   = trim($_POST['catatan_admin'] ?? '');

    if ($kode === '' || ($aksi !== 'setujui' && $aksi !== 'tolak')) {
        header('Location: /admin/peminjaman.php');
        exit;
    }

    $status_baru = ($aksi === 'setujui') ? 'Disetujui' : 'Ditolak';

    $stmt = $conn->prepare("
            UPDATE peminjaman 
            SET status = ?, catatan_admin = ?
            WHERE kode_peminjaman = ?
        ");
    $stmt->bind_param("sss", $status_baru, $catatan, $kode);
    $stmt->execute();

    header("Location: /admin/peminjaman_detail.php?id=" . urlencode($kode));
    exit;
}

// fallback
header('Location: /admin/peminjaman.php');
exit;
