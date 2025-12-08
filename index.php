<?php
/**
 * Index.php - Gateway/Landing Page
 * Redirect logic berdasarkan status login dan role user
 */

require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/helper.php';

// Cek apakah user sudah login
if (is_logged_in()) {
    // Redirect berdasarkan role
    if ($_SESSION['role'] === 'admin') {
        redirect('pages/admin/dashboard.php');
    } elseif ($_SESSION['role'] === 'dokter') {
        redirect('pages/doctor/dashboard.php');
    } else {
        // Role tidak dikenal, logout
        logout();
        redirect('pages/auth/login.php');
    }
} else {
    // Belum login, redirect ke halaman login
    redirect('pages/auth/login.php');
}
