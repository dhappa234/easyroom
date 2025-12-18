<?php
// backend/admin/jadwal_action.php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../functions.php';

require_login();
if (!is_admin()) {
    header('Location: /easyroom/login.php');
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// HAPUS JADWAL (GET)
if ($method === 'GET' && ($_GET['action'] ?? '') === 'delete') {
    $kode = $_GET['kode'] ?? '';

    if ($kode !== '') {
        $stmt = $conn->prepare("DELETE FROM jadwal WHERE kode_jadwal = ?");
        $stmt->bind_param("s", $kode);
        $stmt->execute();
        $stmt->close();
    }

    header('Location: /easyroom/admin/jadwal.php?msg=deleted');
    exit;
}

// TAMBAH JADWAL (POST)
if ($method === 'POST' && ($_POST['action'] ?? '') === 'create') {

    $kode_mk       = trim($_POST['kode_mk'] ?? '');
    $id_dosen      = trim($_POST['id_dosen'] ?? '');
    $kode_ruang    = trim($_POST['kode_ruang'] ?? '');
    $kelas         = trim($_POST['kelas'] ?? '');
    $hari          = trim($_POST['hari'] ?? '');
    $jam_mulai     = trim($_POST['jam_mulai'] ?? '');
    $jam_selesai   = trim($_POST['jam_selesai'] ?? '');
    $semester      = trim($_POST['semester'] ?? '');
    $kuota_peserta = (int)($_POST['kuota_peserta'] ?? 0);

    if ($kode_mk === '' || $id_dosen === '' || $kode_ruang === '' || $kelas === '' || $hari === '' || $jam_mulai === '' || $jam_selesai === '' || $semester === '') {
        // Data kurang lengkap, kembali ke form
        header('Location: /easyroom/admin/jadwal_tambah.php');
        exit;
    }

    // Pastikan format TIME (HH:MM:SS)
    if (preg_match('/^\d{2}:\d{2}$/', $jam_mulai)) $jam_mulai = $jam_mulai . ':00';
    if (preg_match('/^\d{2}:\d{2}$/', $jam_selesai)) $jam_selesai = $jam_selesai . ':00';

    // generate kode_jadwal
    $kode_jadwal = generate_kode('JDW', 7);

    $kuota_masuk = 0; // default 0

    $stmt = $conn->prepare("
        INSERT INTO jadwal 
            (kode_jadwal, kode_mk, id_dosen, kode_ruang, kelas, hari, jam_mulai, jam_selesai, kuota_peserta, kuota_masuk, semester)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "ssssssssiss",
        $kode_jadwal,
        $kode_mk,
        $id_dosen,
        $kode_ruang,
        $kelas,
        $hari,
        $jam_mulai,
        $jam_selesai,
        $kuota_peserta,
        $kuota_masuk,
        $semester
    );
    $stmt->execute();
    $stmt->close();

    header('Location: /easyroom/admin/jadwal.php?msg=created');
    exit;
}

// UPDATE JADWAL (POST)
if ($method === 'POST' && ($_POST['action'] ?? '') === 'update') {
    $kode_jadwal   = trim($_POST['kode_jadwal'] ?? '');
    $kode_mk       = trim($_POST['kode_mk'] ?? '');
    $id_dosen      = trim($_POST['id_dosen'] ?? '');
    $kode_ruang    = trim($_POST['kode_ruang'] ?? '');
    $kelas         = trim($_POST['kelas'] ?? '');
    $hari          = trim($_POST['hari'] ?? '');
    $jam_mulai     = trim($_POST['jam_mulai'] ?? '');
    $jam_selesai   = trim($_POST['jam_selesai'] ?? '');
    $semester      = trim($_POST['semester'] ?? '');
    $kuota_peserta = (int)($_POST['kuota_peserta'] ?? 0);

    if ($kode_jadwal === '' || $kode_mk === '' || $id_dosen === '' || $kode_ruang === '' || $kelas === '' || $hari === '' || $jam_mulai === '' || $jam_selesai === '' || $semester === '') {
        header('Location: /easyroom/admin/jadwal_tambah.php?error=incomplete&kode=' . urlencode($kode_jadwal));
        exit;
    }

    if (preg_match('/^\d{2}:\d{2}$/', $jam_mulai)) $jam_mulai = $jam_mulai . ':00';
    if (preg_match('/^\d{2}:\d{2}$/', $jam_selesai)) $jam_selesai = $jam_selesai . ':00';

    $stmt = $conn->prepare("UPDATE jadwal SET kode_mk = ?, id_dosen = ?, kode_ruang = ?, kelas = ?, hari = ?, jam_mulai = ?, jam_selesai = ?, kuota_peserta = ?, semester = ? WHERE kode_jadwal = ?");
    $stmt->bind_param("sissssisss", $kode_mk, $id_dosen, $kode_ruang, $kelas, $hari, $jam_mulai, $jam_selesai, $kuota_peserta, $semester, $kode_jadwal);
    $stmt->execute();
    $stmt->close();

    header('Location: /easyroom/admin/jadwal.php?msg=updated');
    exit;
}

// Jika tidak cocok dengan semua kondisi di atas
header('Location: /easyroom/admin/jadwal.php');
exit;
