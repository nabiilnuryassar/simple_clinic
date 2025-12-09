<?php
/**
 * Edit Pasien (Admin)
 * Process untuk update data pasien
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/helper.php';

check_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/admin/pasien.php');
    exit;
}

// Validasi CSRF token
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    set_flash('error', 'Invalid CSRF token');
    redirect('pages/admin/pasien.php');
    exit;
}

// Ambil dan validasi input
$id = clean_input($_POST['id'] ?? '');
$nama = clean_input($_POST['nama'] ?? '');
$tanggal_lahir = clean_input($_POST['tanggal_lahir'] ?? '');
$jenis_kelamin = clean_input($_POST['jenis_kelamin'] ?? '');
$no_telepon = clean_input($_POST['no_telepon'] ?? '');
$alamat = clean_input($_POST['alamat'] ?? '');

// Validasi data
if (empty($id) || empty($nama) || empty($tanggal_lahir) || empty($jenis_kelamin) || empty($alamat)) {
    set_flash('error', 'Semua field wajib diisi kecuali No. Telepon');
    redirect('pages/admin/pasien.php');
    exit;
}

try {
    $db = get_db_connection();
    
    // Update data pasien
    $stmt = $db->prepare("
        UPDATE patients 
        SET nama = ?, 
            tanggal_lahir = ?, 
            jenis_kelamin = ?, 
            no_telepon = ?, 
            alamat = ?
        WHERE id = ?
    ");
    
    $stmt->execute([
        $nama,
        $tanggal_lahir,
        $jenis_kelamin,
        $no_telepon,
        $alamat,
        $id
    ]);
    
    if ($stmt->rowCount() > 0) {
        set_flash('success', "Data pasien <strong>$nama</strong> berhasil diupdate!");
    } else {
        set_flash('warning', 'Tidak ada perubahan data atau pasien tidak ditemukan.');
    }
    
} catch (PDOException $e) {
    error_log("Update Patient Error: " . $e->getMessage());
    set_flash('error', 'Gagal mengupdate data pasien. Silakan coba lagi.');
}

redirect('pages/admin/pasien.php');
