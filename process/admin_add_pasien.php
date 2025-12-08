<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/helper.php';

check_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/admin/pasien.php');
}

// Validasi CSRF
if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
    set_flash('error', 'Invalid CSRF token.');
    redirect('pages/admin/pasien.php');
}

// Ambil dan validasi input
$nama = clean_input($_POST['nama']);
$tanggal_lahir = clean_input($_POST['tanggal_lahir']);
$jenis_kelamin = clean_input($_POST['jenis_kelamin']);
$no_telepon = clean_input($_POST['no_telepon']);
$alamat = clean_input($_POST['alamat']);

// Validasi input wajib
if (empty($nama) || empty($tanggal_lahir) || empty($jenis_kelamin) || empty($alamat)) {
    set_flash('error', 'Semua field wajib diisi (kecuali no telepon).');
    redirect('pages/admin/pasien.php');
}

// Validasi jenis kelamin
if (!in_array($jenis_kelamin, ['L', 'P'])) {
    set_flash('error', 'Jenis kelamin tidak valid.');
    redirect('pages/admin/pasien.php');
}

try {
    $db = get_db_connection();
    
    // Generate nomor rekam medis otomatis (format: RM-YYYYMMDD-XXX)
    $date_part = date('Ymd');
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM patients WHERE DATE(created_at) = CURDATE()");
    $stmt->execute();
    $count = $stmt->fetch()['total'] + 1;
    $no_rm = 'RM-' . $date_part . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    
    // Insert data pasien
    $stmt = $db->prepare("
        INSERT INTO patients (no_rm, nama, tanggal_lahir, jenis_kelamin, no_telepon, alamat, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        $no_rm,
        $nama,
        $tanggal_lahir,
        $jenis_kelamin,
        $no_telepon,
        $alamat
    ]);
    
    set_flash('success', 'Data pasien berhasil ditambahkan dengan No. RM: ' . $no_rm);
    redirect('pages/admin/pasien.php');
    
} catch (PDOException $e) {
    error_log("Add Patient Error: " . $e->getMessage());
    set_flash('error', 'Gagal menambahkan data pasien. Silakan coba lagi.');
    redirect('pages/admin/pasien.php');
}
