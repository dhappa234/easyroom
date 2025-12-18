<?php
// backend/dosen/pengajuan_step2_process.php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../functions.php';

require_login();
if (!is_dosen()) {
    header('Location: /easyroom/login.php');
    exit;
}

// Ambil draft dari session (dibuat di step1)
$data = $_SESSION['peminjaman_draft'] ?? null;
if (!$data) {
    // kalau hilang, balik ke ketersediaan ruangan
    header('Location: /easyroom/dosen/ketersediaan_ruang.php');
    exit;
}

$user = current_user();

// ====== SIAPKAN DATA SESUAI STRUKTUR TABEL ======
$kode_peminjaman = generate_kode('PMJ', 8);   // contoh: PMJABC123
$id_dosen        = $user['id'];               // id_dosen dari session
$kode_ruang      = $data['kode_ruang'];       // dari step1
$id_admin        = null;                      // belum ada admin, biarkan NULL
$tanggal         = $data['tanggal'];          // tipe DATE (YYYY-MM-DD)
$waktu_mulai     = $data['waktu_mulai'];      // tipe TIME (HH:MM)
$waktu_selesai   = $data['waktu_selesai'];    // tipe TIME (HH:MM)
$keperluan       = $data['keperluan'];
$file_pengajuan  = $data['file_pengajuan'] ?? null; // path file upload
$status          = 'Menunggu';                // default awal
$catatan_admin   = null;                      // belum ada catatan

// ====== Cek konflik waktu ======
// 1) Cek jadwal perkuliahan yang rutin pada hari yang sama
// Tentukan nama hari Indonesia dari tanggal yang diminta
$dow = (int) date('N', strtotime($tanggal));
$hariMap = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
$hari_nama = $hariMap[$dow] ?? '';

// normalize times to HH:MM:SS if needed
$start = strlen($waktu_mulai) === 5 ? $waktu_mulai . ':00' : $waktu_mulai;
$end   = strlen($waktu_selesai) === 5 ? $waktu_selesai . ':00' : $waktu_selesai;

// Jadwal conflict: overlap if NOT (jam_selesai <= start OR jam_mulai >= end)
$stmt_check_j = $conn->prepare("SELECT COUNT(*) FROM jadwal WHERE kode_ruang = ? AND hari = ? AND NOT (jam_selesai <= ? OR jam_mulai >= ?)");
$stmt_check_j->bind_param('ssss', $kode_ruang, $hari_nama, $start, $end);
$stmt_check_j->execute();
$stmt_check_j->bind_result($conflict_j_count);
$stmt_check_j->fetch();
$stmt_check_j->close();

// Peminjaman (approved) conflict on same date
$stmt_check_p = $conn->prepare("SELECT COUNT(*) FROM peminjaman WHERE kode_ruang = ? AND tanggal = ? AND status = 'Disetujui' AND NOT (waktu_selesai <= ? OR waktu_mulai >= ?)");
$stmt_check_p->bind_param('ssss', $kode_ruang, $tanggal, $start, $end);
$stmt_check_p->execute();
$stmt_check_p->bind_result($conflict_p_count);
$stmt_check_p->fetch();
$stmt_check_p->close();

if ($conflict_j_count > 0) {
    // Tidak boleh mengajukan pada rentang waktu yang bentrok dengan jadwal perkuliahan
    $_SESSION['peminjaman_error'] = 'Waktu yang Anda pilih bentrok dengan jadwal perkuliahan.';
    header('Location: /easyroom/dosen/pengajuan_step2.php?error=conflict_jadwal');
    exit;
}

if ($conflict_p_count > 0) {
    // Sudah ada peminjaman disetujui pada rentang waktu tersebut
    $_SESSION['peminjaman_error'] = 'Waktu yang Anda pilih sudah dibooking oleh peminjaman lain.';
    header('Location: /easyroom/dosen/pengajuan_step2.php?error=conflict_peminjaman');
    exit;
}

// Jika tidak ada konflik, otomatis setujui peminjaman untuk dosen
$status = 'Disetujui';
$catatan_admin = 'Auto-approve: ruang tersedia pada rentang waktu ini.';

// ====== INSERT KE TABEL PEMINJAMAN ======
$stmt = $conn->prepare("
    INSERT INTO peminjaman (
        kode_peminjaman,
        id_dosen,
        kode_ruang,
        id_admin,
        tanggal,
        waktu_mulai,
        waktu_selesai,
        keperluan,
        file_pengajuan,
        status,
        catatan_admin
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "sssssssssss",
    $kode_peminjaman,
    $id_dosen,
    $kode_ruang,
    $id_admin,
    $tanggal,
    $waktu_mulai,
    $waktu_selesai,
    $keperluan,
    $file_pengajuan,
    $status,
    $catatan_admin
);

try {
    $stmt->execute();
    $stmt->close();

    // hapus draft dari session
    unset($_SESSION['peminjaman_draft']);

    // arahkan ke halaman sukses (step3)
    header('Location: /easyroom/dosen/pengajuan_step3.php');
    exit;
} catch (mysqli_sql_exception $e) {
    // untuk debugging, sementara bisa kirim balik ke step2 dengan error
    error_log("DB ERROR PEMINJAMAN: " . $e->getMessage());
    header('Location: /easyroom/dosen/pengajuan_step2.php?error=db_exception');
    exit;
}
