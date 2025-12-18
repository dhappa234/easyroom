<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../functions.php';
require_login();
if (!is_dosen()) {
  header('Location: ../../login.php');
  exit;
}

$tanggal = $_POST['tanggal'] ?? '';
// server-side validasi: tanggal harus antara hari ini dan +3 hari
$today = new DateTimeImmutable('today');
$max = $today->modify('+3 days');
try {
  $dt = new DateTimeImmutable($tanggal);
} catch (Exception $e) {
  die('Tanggal tidak valid.');
}
if ($dt < $today || $dt > $max) {
  die('Peminjaman hanya boleh untuk hari ini sampai 3 hari ke depan.');
}

$user = current_user();

// Ambil data dari form
$data = [
  'id_dosen'      => $user['id'],
  'kode_ruang'    => $_POST['kode_ruang'] ?? '',
  'tanggal'       => $_POST['tanggal'] ?? '',
  'waktu_mulai'   => $_POST['waktu_mulai'] ?? '',
  'waktu_selesai' => $_POST['waktu_selesai'] ?? '',
  'keperluan'     => trim($_POST['keperluan'] ?? ''),
  'file_pengajuan' => '' // default kosong
];

// Validasi data dasar
if (empty($data['kode_ruang']) || empty($data['tanggal']) || empty($data['waktu_mulai']) || empty($data['waktu_selesai']) || empty($data['keperluan'])) {
  // Sebaiknya ada pesan error, tapi untuk sekarang kita redirect saja
  header('Location: /easyroom/dosen/pengajuan_step1.php?ruang=' . $data['kode_ruang'] . '&error=incomplete');
  exit;
}

// Validasi server-side: tanggal harus antara hari ini dan +3 hari
$today = new DateTimeImmutable('today');
$max = $today->modify('+3 days');
try {
  $dt = new DateTimeImmutable($data['tanggal']);
} catch (Exception $e) {
  header('Location: /easyroom/dosen/pengajuan_step1.php?ruang=' . $data['kode_ruang'] . '&error=invalid_date');
  exit;
}
if ($dt < $today || $dt > $max) {
  header('Location: /easyroom/dosen/pengajuan_step1.php?ruang=' . $data['kode_ruang'] . '&error=out_of_range');
  exit;
}

// Handle file upload
if (isset($_FILES['file_pengajuan']) && $_FILES['file_pengajuan']['error'] === UPLOAD_ERR_OK) {
  $file = $_FILES['file_pengajuan'];
  $upload_dir = __DIR__ . '/../../uploads/pengajuan/';
  if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
  }
  $filename = time() . '_' . uniqid() . '_' . basename($file['name']);
  $target_path = $upload_dir . $filename;

  if (move_uploaded_file($file['tmp_name'], $target_path)) {
    // Simpan path relatif ke database
    $data['file_pengajuan'] = 'uploads/pengajuan/' . $filename;
  }
}

// Simpan data ke session untuk dibawa ke step 2
$_SESSION['peminjaman_draft'] = $data;

// Redirect ke halaman konfirmasi (step 2)
header('Location: /easyroom/dosen/pengajuan_step2.php');
exit;
