<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/helper.php';

check_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/admin/antrian.php');
}

// Validasi CSRF
if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
    set_flash('error', 'Invalid CSRF token.');
    redirect('pages/admin/antrian.php');
}

// Ambil dan validasi input
$patient_id = (int)$_POST['patient_id'];
$doctor_id = (int)$_POST['doctor_id'];
$keluhan = clean_input($_POST['keluhan']);

// Validasi input wajib
if (empty($patient_id) || empty($doctor_id) || empty($keluhan)) {
    set_flash('error', 'Semua field wajib diisi.');
    redirect('pages/admin/antrian.php');
}

try {
    $db = get_db_connection();
    
    // Generate nomor antrian (auto increment per hari)
    $stmt = $db->prepare("SELECT MAX(no_antrian) as max_antrian FROM visits WHERE DATE(tanggal_kunjungan) = CURDATE()");
    $stmt->execute();
    $result = $stmt->fetch();
    $no_antrian = ($result['max_antrian'] ?? 0) + 1;
    
    // Insert kunjungan baru
    $stmt = $db->prepare("
        INSERT INTO visits (patient_id, doctor_id, tanggal_kunjungan, no_antrian, keluhan, status, created_at) 
        VALUES (?, ?, NOW(), ?, ?, 'menunggu', NOW())
    ");
    
    $stmt->execute([
        $patient_id,
        $doctor_id,
        $no_antrian,
        $keluhan
    ]);
    
    set_flash('success', 'Antrian berhasil dibuat dengan nomor: ' . $no_antrian);
    redirect('pages/admin/antrian.php');
    
} catch (PDOException $e) {
    error_log("Add Visit Error: " . $e->getMessage());
    set_flash('error', 'Gagal membuat antrian. Silakan coba lagi.');
    redirect('pages/admin/antrian.php');
}
