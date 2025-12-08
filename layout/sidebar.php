<?php
// Sidebar hanya ditampilkan untuk user yang sudah login
if (!is_logged_in()) {
    return;
}

$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'];
?>

<aside class="sidebar">
    <ul class="sidebar-menu">
        <?php if ($role === 'admin'): ?>
            <li>
                <a href="<?php echo base_url('pages/admin/dashboard.php'); ?>" 
                   class="<?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">
                    📊 Dashboard
                </a>
            </li>
            <li>
                <a href="<?php echo base_url('pages/admin/pasien.php'); ?>" 
                   class="<?php echo $current_page === 'pasien.php' ? 'active' : ''; ?>">
                    👥 Data Pasien
                </a>
            </li>
            <li>
                <a href="<?php echo base_url('pages/admin/dokter.php'); ?>" 
                   class="<?php echo $current_page === 'dokter.php' ? 'active' : ''; ?>">
                    👨‍⚕️ Data Dokter
                </a>
            </li>
            <li>
                <a href="<?php echo base_url('pages/admin/antrian.php'); ?>" 
                   class="<?php echo $current_page === 'antrian.php' ? 'active' : ''; ?>">
                    📋 Antrian Kunjungan
                </a>
            </li>
        <?php elseif ($role === 'dokter'): ?>
            <li>
                <a href="<?php echo base_url('pages/doctor/dashboard.php'); ?>" 
                   class="<?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">
                    📊 Dashboard
                </a>
            </li>
            <li>
                <a href="<?php echo base_url('pages/doctor/periksa.php'); ?>" 
                   class="<?php echo $current_page === 'periksa.php' ? 'active' : ''; ?>">
                    🩺 Periksa Pasien
                </a>
            </li>
        <?php endif; ?>
    </ul>
</aside>
