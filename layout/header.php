<?php
// Load konfigurasi
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/helper.php';

$page_title = isset($page_title) ? $page_title : 'Klinik Mutiara';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo clean_input($page_title); ?> - Sistem Informasi Klinik</title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
</head>
<body>
    <?php if (is_logged_in()): ?>
    <header class="header">
        <nav class="navbar">
            <a href="<?php echo base_url('index.php'); ?>" class="navbar-brand">🏥 Klinik Mutiara</a>
            <ul class="navbar-menu">
                <li>
                    <strong><?php echo clean_input($_SESSION['nama']); ?></strong>
                    <span class="badge badge-primary"><?php echo clean_input($_SESSION['role']); ?></span>
                </li>
                <li>
                    <a href="<?php echo base_url('process/auth_logout.php'); ?>">Logout</a>
                </li>
            </ul>
        </nav>
    </header>
    <?php endif; ?>
    
    <?php
    // Tampilkan flash message jika ada
    $flash = get_flash();
    if ($flash):
    ?>
    <div class="alert alert-<?php echo $flash['type']; ?>">
        <?php echo $flash['message']; ?>
    </div>
    <?php endif; ?>
