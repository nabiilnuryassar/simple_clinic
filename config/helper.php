<?php
/**
 * Helper Functions
 * Fungsi-fungsi utility yang digunakan di seluruh aplikasi
 */

// Sanitasi input untuk mencegah XSS
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Base URL helper
function base_url($path = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    // Auto-detect base path (works for both PHP built-in server and XAMPP subfolder)
    $script_name = dirname($_SERVER['SCRIPT_NAME']);
    $base = ($script_name === '/' || $script_name === '\\') ? '' : $script_name;
    
    $path = ltrim($path, '/');
    
    if (!empty($path)) {
        return $protocol . '://' . $host . $base . '/' . $path;
    }
    
    return $protocol . '://' . $host . $base . '/';
}

// Redirect helper
function redirect($path) {
    header('Location: ' . base_url($path));
    exit;
}

// Flash message (session-based notification)
function set_flash($type, $message) {
    $_SESSION['flash_type'] = $type; // success, error, warning, info
    $_SESSION['flash_message'] = $message;
}

function get_flash() {
    if (isset($_SESSION['flash_message'])) {
        $flash = [
            'type' => $_SESSION['flash_type'],
            'message' => $_SESSION['flash_message']
        ];
        unset($_SESSION['flash_type']);
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

// Format tanggal Indonesia
function format_date($date) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $timestamp = strtotime($date);
    $day = date('d', $timestamp);
    $month = $bulan[(int)date('m', $timestamp)];
    $year = date('Y', $timestamp);
    
    return $day . ' ' . $month . ' ' . $year;
}

// Validasi email
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Hash password (gunakan PHP password_hash - bcrypt)
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Verify password
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}
