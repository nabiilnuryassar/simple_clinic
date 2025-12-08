<?php
/**
 * Session Configuration & Security
 * Mengatur session dengan standar keamanan modern
 */

// Cegah session fixation attack
if (session_status() === PHP_SESSION_NONE) {
    // Konfigurasi session sebelum session_start()
    ini_set('session.cookie_httponly', 1);  // Cegah akses cookie via JavaScript
    ini_set('session.cookie_secure', 0);     // Set 1 jika pakai HTTPS
    ini_set('session.use_strict_mode', 1);   // Tolak session ID tidak valid
    ini_set('session.cookie_samesite', 'Strict'); // CSRF protection
    
    session_name('KLINIK_SESSION');
    session_start();
}

// Regenerate session ID untuk mencegah session fixation
function regenerate_session() {
    session_regenerate_id(true);
}

// Cek apakah user sudah login
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

// Cek role user
function check_role($allowed_roles) {
    if (!is_logged_in()) {
        header('Location: /pages/auth/login.php');
        exit;
    }
    
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        die("Access Denied: Anda tidak memiliki akses ke halaman ini.");
    }
}

// Logout function
function logout() {
    $_SESSION = [];
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}

// CSRF Token Generation
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF Token Validation
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
