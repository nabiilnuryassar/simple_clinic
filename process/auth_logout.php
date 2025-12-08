<?php
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/helper.php';

// Logout user
logout();

// Set flash message
set_flash('info', 'Anda telah berhasil logout.');

// Redirect ke halaman login
redirect('pages/auth/login.php');
