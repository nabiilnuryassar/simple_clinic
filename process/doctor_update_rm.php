<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/helper.php';

check_role(['dokter']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/doctor/periksa.php');
}

// Validasi CSRF
if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
    set_flash('error', 'Invalid CSRF token.');
    redirect('pages/doctor/periksa.php');
}

// Ambil dan validasi input
$visit_id = (int)$_POST['visit_id'];
$patient_id = (int)$_POST['patient_id'];
$anamnesa = clean_input($_POST['anamnesa']);
$diagnosa = clean_input($_POST['diagnosa']);
$tindakan = clean_input($_POST['tindakan']);
$resep = clean_input($_POST['resep']);
$catatan = clean_input($_POST['catatan']);

// Validasi input wajib
if (empty($visit_id) || empty($patient_id) || empty($anamnesa) || empty($diagnosa)) {
    set_flash('error', 'Anamnesa dan diagnosa wajib diisi.');
    redirect('pages/doctor/periksa.php');
}

try {
    $db = get_db_connection();
    
    // Ambil doctor_id yang sedang login
    $stmt = $db->prepare("SELECT id FROM doctors WHERE nama = ? LIMIT 1");
    $stmt->execute([$_SESSION['nama']]);
    $doctor_data = $stmt->fetch();
    $doctor_id = $doctor_data['id'] ?? 0;
    
    if (!$doctor_id) {
        set_flash('error', 'Data dokter tidak ditemukan.');
        redirect('pages/doctor/periksa.php');
    }
    
    // Begin transaction
    $db->beginTransaction();
    
    // Insert rekam medis
    $stmt = $db->prepare("
        INSERT INTO medical_records (visit_id, patient_id, doctor_id, anamnesa, diagnosa, tindakan, resep, catatan, tanggal_periksa, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");
    $stmt->execute([
        $visit_id,
        $patient_id,
        $doctor_id,
        $anamnesa,
        $diagnosa,
        $tindakan,
        $resep,
        $catatan
    ]);
    
    // Update status kunjungan menjadi 'selesai'
    $stmt = $db->prepare("UPDATE visits SET status = 'selesai' WHERE id = ?");
    $stmt->execute([$visit_id]);
    
    // Commit transaction
    $db->commit();
    
    set_flash('success', 'Pemeriksaan berhasil disimpan. Pasien selesai diperiksa.');
    redirect('pages/doctor/periksa.php');
    
} catch (PDOException $e) {
    // Rollback jika ada error
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Update Medical Record Error: " . $e->getMessage());
    set_flash('error', 'Gagal menyimpan pemeriksaan. Silakan coba lagi.');
    redirect('pages/doctor/periksa.php');
}
