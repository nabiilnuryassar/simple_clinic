<?php
/**
 * Delete Pasien (Admin)
 * Process untuk hapus data pasien
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

$id = clean_input($_POST['id'] ?? '');

if (empty($id)) {
    set_flash('error', 'ID pasien tidak valid');
    redirect('pages/admin/pasien.php');
    exit;
}

try {
    $db = get_db_connection();
    
    // Ambil nama pasien sebelum dihapus
    $stmt = $db->prepare("SELECT nama FROM patients WHERE id = ?");
    $stmt->execute([$id]);
    $pasien = $stmt->fetch();
    
    if (!$pasien) {
        set_flash('error', 'Pasien tidak ditemukan');
        redirect('pages/admin/pasien.php');
        exit;
    }
    
    // Hapus data pasien (CASCADE akan hapus medical_records & visits otomatis)
    $stmt = $db->prepare("DELETE FROM patients WHERE id = ?");
    $stmt->execute([$id]);
    
    set_flash('success', 'Data pasien <strong>' . htmlspecialchars($pasien['nama'], ENT_QUOTES, 'UTF-8') . '</strong> berhasil dihapus!');
    
} catch (PDOException $e) {
    error_log("Delete Patient Error: " . $e->getMessage());
    set_flash('error', 'Gagal menghapus data pasien. Mungkin masih ada data terkait.');
}

redirect('pages/admin/pasien.php');
