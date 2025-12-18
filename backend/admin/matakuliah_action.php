<?php
// backend/admin/matakuliah_action.php

// Pastikan file konfigurasi database dan fungsi utama dimuat
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../functions.php';

// Cek status login dan role admin
require_login();
if (!is_admin()) {
    header('Location: ../../login.php');
    exit;
}

// Hanya proses jika permintaan adalah POST dan action adalah 'add'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    
    // 1. Ambil dan bersihkan data dari form
    $kode_mk = trim($_POST['kode_mk'] ?? '');
    $nama_mk = trim($_POST['nama_mk'] ?? '');
    // Konversi SKS ke integer untuk memastikan tipe data yang benar
    $sks     = (int)($_POST['sks'] ?? 0);

    // 2. Validasi data dasar
    if (empty($kode_mk) || empty($nama_mk) || $sks <= 0) {
        // Jika ada data yang tidak valid, kembali ke halaman tambah dengan pesan error
        header('Location: ../../admin/matakuliah_tambah.php?error=invalid');
        exit;
    }

    // 3. Persiapkan dan jalankan query INSERT (menggunakan prepared statement)
    $sql = "INSERT INTO matakuliah (kode_mk, nama_mk, sks) VALUES (?, ?, ?)";
    
    // Cek apakah prepare statement berhasil
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameter: "s" untuk string (kode_mk, nama_mk), "i" untuk integer (sks)
        $stmt->bind_param("ssi", $kode_mk, $nama_mk, $sks);
        
        if ($stmt->execute()) {
            // Berhasil: Redirect kembali ke halaman jadwal dengan pesan sukses
            header('Location: ../../admin/jadwal.php?msg=mk_created');
            exit;
        } else {
            // Gagal eksekusi (contoh: Kode MK sudah ada / duplicate entry)
            // Redirect kembali ke halaman tambah dengan pesan error database
            header('Location: ../../admin/matakuliah_tambah.php?error=db_error');
            exit;
        }
        
        // Tutup statement
        $stmt->close();
    } else {
        // Gagal menyiapkan statement SQL
        error_log("Failed to prepare statement: " . $conn->error);
        header('Location: ../../admin/matakuliah_tambah.php?error=db_error');
        exit;
    }
}

// Jika akses langsung ke file action tanpa POST, redirect ke halaman utama jadwal
header('Location: ../../admin/jadwal.php');
exit;
?>