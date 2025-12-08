<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/helper.php';

check_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/admin/dokter.php');
}

// Validasi CSRF
if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
    set_flash('error', 'Invalid CSRF token.');
    redirect('pages/admin/dokter.php');
}

// Ambil dan validasi input
$nama = clean_input($_POST['nama']);
$spesialisasi = clean_input($_POST['spesialisasi']);
$no_telepon = clean_input($_POST['no_telepon']);
$email = clean_input($_POST['email']);
$username = clean_input($_POST['username']);
$password = $_POST['password'];

// Validasi input wajib
if (empty($nama) || empty($spesialisasi) || empty($no_telepon) || empty($username) || empty($password)) {
    set_flash('error', 'Field yang bertanda * wajib diisi.');
    redirect('pages/admin/dokter.php');
}

// Validasi email jika diisi
if (!empty($email) && !is_valid_email($email)) {
    set_flash('error', 'Format email tidak valid.');
    redirect('pages/admin/dokter.php');
}

try {
    $db = get_db_connection();
    
    // Cek apakah username sudah digunakan
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        set_flash('error', 'Username sudah digunakan. Silakan pilih username lain.');
        redirect('pages/admin/dokter.php');
    }
    
    // Hash password
    $hashed_password = hash_password($password);
    
    // Begin transaction
    $db->beginTransaction();
    
    // Insert ke tabel doctors
    $stmt = $db->prepare("
        INSERT INTO doctors (nama, spesialisasi, no_telepon, email, status, created_at) 
        VALUES (?, ?, ?, ?, 'aktif', NOW())
    ");
    $stmt->execute([$nama, $spesialisasi, $no_telepon, $email]);
    $doctor_id = $db->lastInsertId();
    
    // Insert ke tabel users
    $stmt = $db->prepare("
        INSERT INTO users (username, password, nama, role, created_at) 
        VALUES (?, ?, ?, 'dokter', NOW())
    ");
    $stmt->execute([$username, $hashed_password, $nama]);
    
    // Commit transaction
    $db->commit();
    
    set_flash('success', 'Data dokter berhasil ditambahkan.');
    redirect('pages/admin/dokter.php');
    
} catch (PDOException $e) {
    // Rollback jika ada error
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Add Doctor Error: " . $e->getMessage());
    set_flash('error', 'Gagal menambahkan data dokter. Silakan coba lagi.');
    redirect('pages/admin/dokter.php');
}
