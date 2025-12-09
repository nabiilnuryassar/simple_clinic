<?php
/**
 * Delete Dokter (Admin)
 * Process untuk hapus data dokter
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

$id = clean_input($_POST['id'] ?? '');

if (empty($id)) {
    set_flash('error', 'ID dokter tidak valid');
    redirect('pages/admin/dokter.php');
    exit;
}

try {
    $db = get_db_connection();
    
    // Ambil nama dokter sebelum dihapus
    $stmt = $db->prepare("SELECT nama FROM doctors WHERE id = ?");
    $stmt->execute([$id]);
    $dokter = $stmt->fetch();
    
    if (!$dokter) {
        set_flash('error', 'Dokter tidak ditemukan');
        redirect('pages/admin/dokter.php');
        exit;
    }
    
    // Hapus data dokter (CASCADE akan hapus medical_records & visits otomatis)
    $stmt = $db->prepare("DELETE FROM doctors WHERE id = ?");
    $stmt->execute([$id]);
    
    set_flash('success', 'Data dokter <strong>' . htmlspecialchars($dokter['nama'], ENT_QUOTES, 'UTF-8') . '</strong> berhasil dihapus!');
    
} catch (PDOException $e) {
    error_log("Delete Doctor Error: " . $e->getMessage());
    set_flash('error', 'Gagal menghapus data dokter. Mungkin masih ada data terkait.');
}

redirect('pages/admin/dokter.php');
