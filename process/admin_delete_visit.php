<?php
/**
 * Delete Visit/Antrian (Admin)
 * Process untuk hapus data antrian kunjungan
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/helper.php';

check_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/admin/antrian.php');
    exit;
}

// Validasi CSRF token
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    set_flash('error', 'Invalid CSRF token');
    redirect('pages/admin/antrian.php');
    exit;
}

$id = clean_input($_POST['id'] ?? '');

if (empty($id)) {
    set_flash('error', 'ID antrian tidak valid');
    redirect('pages/admin/antrian.php');
    exit;
}

try {
    $db = get_db_connection();
    
    // Ambil info antrian sebelum dihapus
    $stmt = $db->prepare("
        SELECT v.no_antrian, p.nama as pasien_nama 
        FROM visits v
        JOIN patients p ON v.patient_id = p.id
        WHERE v.id = ?
    ");
    $stmt->execute([$id]);
    $visit = $stmt->fetch();
    
    if (!$visit) {
        set_flash('error', 'Antrian tidak ditemukan');
        redirect('pages/admin/antrian.php');
        exit;
    }
    
    // Hapus data antrian (CASCADE akan hapus medical_records otomatis)
    $stmt = $db->prepare("DELETE FROM visits WHERE id = ?");
    $stmt->execute([$id]);
    
    set_flash('success', "Antrian <strong>#{$visit['no_antrian']}</strong> pasien {$visit['pasien_nama']} berhasil dihapus!");
    
} catch (PDOException $e) {
    error_log("Delete Visit Error: " . $e->getMessage());
    set_flash('error', 'Gagal menghapus data antrian.');
}

redirect('pages/admin/antrian.php');
