<?php
/**
 * URL Helper Functions
 * Helper untuk menangani URL dan path dalam aplikasi
 */

/**
 * Mendapatkan base URL aplikasi
 * @return string
 */
function base_url($path = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    // Ambil directory dari SCRIPT_NAME
    $script_path = dirname($_SERVER['SCRIPT_NAME']);
    $base_path = ($script_path === '/' || $script_path === '\\') ? '' : $script_path;
    
    $base = $protocol . '://' . $host . $base_path;
    
    if (!empty($path)) {
        $path = ltrim($path, '/');
        return $base . '/' . $path;
    }
    
    return $base;
}

/**
 * Redirect ke URL tertentu
 * @param string $path Path tujuan (relatif dari base URL)
 * @param int $status_code HTTP status code (default: 302)
 */
function redirect($path, $status_code = 302) {
    $url = base_url($path);
    header("Location: $url", true, $status_code);
    exit();
}

/**
 * Sanitasi input untuk mencegah XSS
 * @param string $data
 * @return string
 */
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Sanitasi output untuk tampilan HTML
 * @param string $string
 * @return string
 */
function escape_html($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 * @return string
 */
function generate_csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Validasi CSRF token
 * @param string $token
 * @return bool
 */
function verify_csrf_token($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate CSRF input hidden field
 * @return string
 */
function csrf_field() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Flash message setter
 * @param string $key
 * @param string $message
 */
function set_flash($key, $message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['flash'][$key] = $message;
}

/**
 * Flash message getter dan hapus setelah dibaca
 * @param string $key
 * @return string|null
 */
function get_flash($key) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    
    return null;
}

/**
 * Check apakah user sudah login
 * @return bool
 */
function is_logged_in() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check role user
 * @param string $role
 * @return bool
 */
function has_role($role) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

/**
 * Require login, redirect jika belum login
 */
function require_login() {
    if (!is_logged_in()) {
        set_flash('error', 'Silakan login terlebih dahulu.');
        redirect('pages/auth/login.php');
    }
}

/**
 * Require role tertentu, redirect jika tidak sesuai
 * @param string $role
 */
function require_role($role) {
    require_login();
    
    if (!has_role($role)) {
        set_flash('error', 'Anda tidak memiliki akses ke halaman ini.');
        redirect('index.php');
    }
}

/**
 * Format tanggal Indonesia
 * @param string $date
 * @param string $format
 * @return string
 */
function format_date($date, $format = 'd/m/Y') {
    if (empty($date) || $date === '0000-00-00') {
        return '-';
    }
    
    $timestamp = strtotime($date);
    return date($format, $timestamp);
}

/**
 * Format tanggal dan waktu Indonesia
 * @param string $datetime
 * @return string
 */
function format_datetime($datetime) {
    if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
        return '-';
    }
    
    $timestamp = strtotime($datetime);
    return date('d/m/Y H:i', $timestamp);
}
