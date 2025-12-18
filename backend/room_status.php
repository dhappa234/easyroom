<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

// require logged-in user to access this endpoint
require_login();

header('Content-Type: application/json; charset=utf-8');

$kode = $_GET['kode'] ?? '';
if ($kode === '') {
  echo json_encode(['error' => 'missing_kode']);
  exit;
}

// accept local date/time from client (to respect user's clock). Fallback to server time.
$today = date('Y-m-d');
$now_time = date('H:i:s');
$local_date = $_GET['local_date'] ?? $today;
$local_time = $_GET['local_time'] ?? $now_time;

// normalize time string to HH:MM:SS
if (strlen($local_time) === 5) $local_time .= ':00';

// build DateTime from local date/time (fallback to server if invalid)
try {
  $dt = new DateTimeImmutable($local_date . ' ' . $local_time);
} catch (Exception $e) {
  $dt = new DateTimeImmutable();
  $local_date = $dt->format('Y-m-d');
  $local_time = $dt->format('H:i:s');
}

$start_dt = $dt; // start from client local time
$end_dt = $dt->modify('+15 minutes'); // 15-minute window forward
$start_time = $start_dt->format('H:i:s');
$end_time = $end_dt->format('H:i:s');

// map PHP day number for the provided local date to Indonesian name used in jadwal.hari
$map = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
$hari = $map[(int)$dt->format('N')];

$resp = [
  'kode' => $kode,
  'status' => 'available',
  'reason' => null,
  'current' => null,
  'next' => null,
];

// 1) check peminjaman approved for today overlapping now
$stmt = $conn->prepare("SELECT p.kode_peminjaman, p.tanggal, p.waktu_mulai, p.waktu_selesai, p.keperluan, d.nama_dosen AS nama_dosen_peminjam
  FROM peminjaman p
  LEFT JOIN dosen d ON p.id_dosen = d.id_dosen
  WHERE p.kode_ruang = ? AND p.tanggal = ? AND p.status = 'Disetujui' AND NOT (p.waktu_selesai <= ? OR p.waktu_mulai >= ?) LIMIT 1");
$stmt->bind_param('ssss', $kode, $local_date, $start_time, $end_time);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
if ($row) {
  $resp['status'] = 'unavailable';
  $resp['current'] = $row;
  echo json_encode($resp);
  exit;
}
$stmt->close();

// 2) check jadwal for today (weekday name) where current time overlaps
$stmt2 = $conn->prepare("SELECT j.*, mk.nama_mk AS nama_matakuliah, d.nama_dosen AS nama_dosen
  FROM jadwal j
  LEFT JOIN matakuliah mk ON j.kode_mk = mk.kode_mk
  LEFT JOIN dosen d ON j.id_dosen = d.id_dosen
  WHERE j.kode_ruang = ? AND j.hari = ? AND NOT (j.jam_selesai <= ? OR j.jam_mulai >= ?) LIMIT 1");
$stmt2->bind_param('ssss', $kode, $hari, $start_time, $end_time);
$stmt2->execute();
$res2 = $stmt2->get_result();
$rj = $res2->fetch_assoc();
if ($rj) {
  $resp['status'] = 'unavailable';
  $resp['current'] = $rj;
  echo json_encode($resp);
  exit;
}
$stmt2->close();

// 3) find next approved booking (today or future)
$stmt3 = $conn->prepare("SELECT p.kode_peminjaman, p.tanggal, p.waktu_mulai, p.waktu_selesai, p.keperluan, d.nama_dosen AS nama_dosen_peminjam
  FROM peminjaman p
  LEFT JOIN dosen d ON p.id_dosen = d.id_dosen
  WHERE p.kode_ruang = ? AND p.status = 'Disetujui' AND p.tanggal >= ?
  ORDER BY p.tanggal, p.waktu_mulai LIMIT 1");
$stmt3->bind_param('ss', $kode, $local_date);
$stmt3->execute();
$res3 = $stmt3->get_result();
if ($n = $res3->fetch_assoc()) {
  $resp['next'] = $n;
}
$stmt3->close();

echo json_encode($resp);
