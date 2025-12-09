<?php
/**
 * Edit Dokter (Admin)
 * Process untuk update data dokter
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/helper.php';

check_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/admin/dokter.php');
    exit;
}

// Validasi CSRF token
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    set_flash('error', 'Invalid CSRF token');
    redirect('pages/admin/dokter.php');
    exit;
}

// Ambil dan validasi input
$id = clean_input($_POST['id'] ?? '');
$nama = clean_input($_POST['nama'] ?? '');
$spesialisasi = clean_input($_POST['spesialisasi'] ?? '');
$no_telepon = clean_input($_POST['no_telepon'] ?? '');
$email = clean_input($_POST['email'] ?? '');
$status = clean_input($_POST['status'] ?? 'aktif');

// Validasi data
if (empty($id) || empty($nama) || empty($spesialisasi) || empty($no_telepon)) {
    set_flash('error', 'Nama, spesialisasi, dan no telepon wajib diisi');
    redirect('pages/admin/dokter.php');
    exit;
}

try {
    $db = get_db_connection();
    
    // Update data dokter
    $stmt = $db->prepare("
        UPDATE doctors 
        SET nama = ?, 
            spesialisasi = ?, 
            no_telepon = ?, 
            email = ?,
            status = ?
        WHERE id = ?
    ");
    
    $stmt->execute([
        $nama,
        $spesialisasi,
        $no_telepon,
        $email,
        $status,
        $id
    ]);
    
    if ($stmt->rowCount() > 0) {
        set_flash('success', "Data dokter <strong>$nama</strong> berhasil diupdate!");
    } else {
        set_flash('warning', 'Tidak ada perubahan data atau dokter tidak ditemukan.');
    }
    
} catch (PDOException $e) {
    error_log("Update Doctor Error: " . $e->getMessage());
    set_flash('error', 'Gagal mengupdate data dokter. Silakan coba lagi.');
}

redirect('pages/admin/dokter.php');
