<?php
// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login
function is_logged_in(): bool
{
    // cek adanya data user + id (lebih aman)
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
}

// Paksa login kalau belum
function require_login(): void
{
    if (!is_logged_in()) {
        // SELALU arahkan ke /login.php (absolute path),
        // supaya dari /admin, /dosen, /mahasiswa tetap ke login utama
        header('Location: /login.php');
        exit;
    }
}

// Ambil data user yang sedang login
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

// Role helper
function is_admin(): bool
{
    return isset($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'admin';
}

function is_dosen(): bool
{
    return isset($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'dosen';
}

function is_mahasiswa(): bool
{
    return isset($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'mahasiswa';
}

function generate_id_admin($conn)
{
    $query = mysqli_query($conn, "SELECT id_admin FROM admin ORDER BY id_admin DESC LIMIT 1");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        $last_code = $data['id_admin'];
        $number = (int) substr($last_code, 4);
        $number++;
    } else {
        $number = 1;
    }

    return 'ADM' . sprintf('%07d', $number);
}

function generate_id_dosen($conn)
{
    $query = mysqli_query($conn, "SELECT id_dosen FROM dosen ORDER BY id_dosen DESC LIMIT 1");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        $last_code = $data['id_dosen'];
        $number = (int) substr($last_code, 4);
        $number++;
    } else {
        $number = 1;
    }

    return 'DSN' . sprintf('%07d', $number);
}

function generate_id_mahasiswa($conn)
{
    $query = mysqli_query($conn, "SELECT id_mahasiswa FROM mahasiswa ORDER BY id_mahasiswa DESC LIMIT 1");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        $last_code = $data['id_mahasiswa'];
        $number = (int) substr($last_code, 4);
        $number++;
    } else {
        $number = 1;
    }

    return 'MHS' . sprintf('%07d', $number);
}
function generate_kode(string $prefix = 'K', int $length = 8): string
{
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $rand  = '';
    for ($i = 0; $i < $length; $i++) {
        $rand .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $prefix . $rand;
}

/**
 * Kembalikan tanggal berikutnya (YYYY-MM-DD) untuk nama hari bahasa Indonesia (Senin..Sabtu, Minggu)
 */
function get_next_date_for_day(string $dayName): string
{
    $map = [
        'Minggu' => 0,
        'Senin' => 1,
        'Selasa' => 2,
        'Rabu' => 3,
        'Kamis' => 4,
        'Jumat' => 5,
        'Sabtu' => 6
    ];
    $dayName = trim($dayName);
    if (!isset($map[$dayName])) return date('Y-m-d');

    $target = $map[$dayName];
    $today = (int)date('w');
    $diff = ($target - $today + 7) % 7;
    // jika hari sama, anggap hari ini (diff = 0)
    $dt = new DateTime();
    if ($diff > 0) {
        $dt->modify("+{$diff} days");
    }
    return $dt->format('Y-m-d');
}

/**
 * Safe format jam (hh:mm) — menghindari substr(null,...)
 */
function safe_time_short(?string $t): string
{
    if (empty($t)) return '-';
    return htmlspecialchars(substr($t, 0, 5));
}
