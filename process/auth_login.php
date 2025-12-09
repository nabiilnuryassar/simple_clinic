<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/helper.php';

// Cek method harus POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/auth/login.php');
}

// Validasi CSRF Token
if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
    set_flash('error', 'Invalid CSRF token. Silakan coba lagi.');
    redirect('pages/auth/login.php');
}

// Ambil dan sanitasi input
$username = clean_input($_POST['username']);
$password = $_POST['password']; // Password tidak di-sanitasi dulu

// Validasi input tidak boleh kosong
if (empty($username) || empty($password)) {
    set_flash('error', 'Username dan password harus diisi.');
    redirect('pages/auth/login.php');
}

try {
    $db = get_db_connection();
    
    // Query dengan prepared statement untuk mencegah SQL injection
    $stmt = $db->prepare("SELECT id, username, password, nama, role FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    // Cek apakah user ditemukan dan password cocok
    if ($user && verify_password($password, $user['password'])) {
        // Regenerate session ID untuk mencegah session fixation
        regenerate_session();
        
        // Set session data
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_time'] = time();
        
        // Jika user adalah dokter, coba temukan doctor_id dan simpan di session
        if ($user['role'] === 'dokter') {
            try {
                // Prioritas: cari berdasarkan relasi user_id (jika schema sudah diupdate)
                $stmtDoc = $db->prepare("SELECT id FROM doctors WHERE user_id = ? LIMIT 1");
                $stmtDoc->execute([$user['id']]);
                $doc = $stmtDoc->fetch();

                if (!$doc) {
                    // Fallback: cari berdasarkan nama (backward compatibility)
                    $stmtDoc = $db->prepare("SELECT id FROM doctors WHERE nama = ? LIMIT 1");
                    $stmtDoc->execute([$user['nama']]);
                    $doc = $stmtDoc->fetch();
                }

                $_SESSION['doctor_id'] = $doc['id'] ?? null;
            } catch (PDOException $e) {
                // Jika gagal, tetap biarkan login berhasil tetapi tanpa doctor_id di session
                error_log("Get doctor_id on login error: " . $e->getMessage());
                $_SESSION['doctor_id'] = null;
            }
        }
        
        // Redirect berdasarkan role
        if ($user['role'] === 'admin') {
            set_flash('success', 'Selamat datang, ' . $user['nama'] . '!');
            redirect('pages/admin/dashboard.php');
        } else {
            set_flash('success', 'Selamat datang, Dr. ' . $user['nama'] . '!');
            redirect('pages/doctor/dashboard.php');
        }
    } else {
        // Login gagal
        set_flash('error', 'Username atau password salah.');
        redirect('pages/auth/login.php');
    }
    
} catch (PDOException $e) {
    error_log("Login Error: " . $e->getMessage());
    set_flash('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
    redirect('pages/auth/login.php');
}
