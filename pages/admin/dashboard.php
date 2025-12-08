<?php
$page_title = 'Dashboard Admin';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/helper.php';

// Proteksi halaman: hanya admin yang bisa akses
check_role(['admin']);

// Ambil statistik data
try {
    $db = get_db_connection();
    
    // Hitung total pasien
    $stmt = $db->query("SELECT COUNT(*) as total FROM patients");
    $total_pasien = $stmt->fetch()['total'];
    
    // Hitung total dokter
    $stmt = $db->query("SELECT COUNT(*) as total FROM doctors");
    $total_dokter = $stmt->fetch()['total'];
    
    // Hitung kunjungan hari ini
    $stmt = $db->query("SELECT COUNT(*) as total FROM visits WHERE DATE(tanggal_kunjungan) = CURDATE()");
    $kunjungan_hari_ini = $stmt->fetch()['total'];
    
    // Hitung total kunjungan
    $stmt = $db->query("SELECT COUNT(*) as total FROM visits");
    $total_kunjungan = $stmt->fetch()['total'];
    
} catch (PDOException $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    $total_pasien = $total_dokter = $kunjungan_hari_ini = $total_kunjungan = 0;
}

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<main class="main-content">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Dashboard Administrator</h1>
        </div>
        <p>Selamat datang, <strong><?php echo clean_input($_SESSION['nama']); ?></strong>!</p>
        <p>Sistem Informasi Klinik X - Panel Administrasi</p>
    </div>
    
    <!-- Statistik Cards -->
    <div class="stats-grid">
        <div class="stat-card" style="border-left-color: #2563eb;">
            <div class="stat-value"><?php echo $total_pasien; ?></div>
            <div class="stat-label">Total Pasien Terdaftar</div>
        </div>
        
        <div class="stat-card" style="border-left-color: #10b981;">
            <div class="stat-value"><?php echo $total_dokter; ?></div>
            <div class="stat-label">Total Dokter Aktif</div>
        </div>
        
        <div class="stat-card" style="border-left-color: #f59e0b;">
            <div class="stat-value"><?php echo $kunjungan_hari_ini; ?></div>
            <div class="stat-label">Kunjungan Hari Ini</div>
        </div>
        
        <div class="stat-card" style="border-left-color: #06b6d4;">
            <div class="stat-value"><?php echo $total_kunjungan; ?></div>
            <div class="stat-label">Total Kunjungan</div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Menu Cepat</h2>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo base_url('pages/admin/pasien.php'); ?>" class="btn btn-primary">
                👥 Kelola Pasien
            </a>
            <a href="<?php echo base_url('pages/admin/dokter.php'); ?>" class="btn btn-secondary">
                👨‍⚕️ Kelola Dokter
            </a>
            <a href="<?php echo base_url('pages/admin/antrian.php'); ?>" class="btn btn-warning">
                📋 Buat Antrian Kunjungan
            </a>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
