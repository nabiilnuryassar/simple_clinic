<?php
$page_title = 'Dashboard Dokter';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/helper.php';

check_role(['dokter']);

// Ambil data dokter yang sedang login
try {
    $db = get_db_connection();
    
    // Prioritas: gunakan doctor_id yang disimpan di session (set saat login)
    $doctor_id = $_SESSION['doctor_id'] ?? null;

    // Jika tidak ada di session, fallback: cari berdasarkan nama (backward compatibility)
    if (empty($doctor_id)) {
        $stmt = $db->prepare("SELECT id FROM doctors WHERE nama = ? LIMIT 1");
        $stmt->execute([$_SESSION['nama']]);
        $doctor_data = $stmt->fetch();
        $doctor_id = $doctor_data['id'] ?? 0;
    }
    
    // Hitung pasien hari ini untuk dokter ini
    $stmt = $db->prepare("
        SELECT COUNT(*) as total 
        FROM visits 
        WHERE doctor_id = ? AND DATE(tanggal_kunjungan) = CURDATE()
    ");
    $stmt->execute([$doctor_id]);
    $pasien_hari_ini = $stmt->fetch()['total'];
    
    // Hitung pasien menunggu
    $stmt = $db->prepare("
        SELECT COUNT(*) as total 
        FROM visits 
        WHERE doctor_id = ? AND DATE(tanggal_kunjungan) = CURDATE() AND status = 'menunggu'
    ");
    $stmt->execute([$doctor_id]);
    $pasien_menunggu = $stmt->fetch()['total'];
    
    // Hitung pasien selesai hari ini
    $stmt = $db->prepare("
        SELECT COUNT(*) as total 
        FROM visits 
        WHERE doctor_id = ? AND DATE(tanggal_kunjungan) = CURDATE() AND status = 'selesai'
    ");
    $stmt->execute([$doctor_id]);
    $pasien_selesai = $stmt->fetch()['total'];
    
} catch (PDOException $e) {
    error_log("Dashboard Doctor Error: " . $e->getMessage());
    $pasien_hari_ini = $pasien_menunggu = $pasien_selesai = 0;
}

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<main class="main-content">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Dashboard Dokter</h1>
        </div>
        <p>Selamat datang, <strong>Dr. <?php echo clean_input($_SESSION['nama']); ?></strong>!</p>
        <p>Panel Dokter - Sistem Informasi Klinik Mutiara</p>
    </div>
    
    <!-- Statistik Cards -->
    <div class="stats-grid">
        <div class="stat-card" style="border-left-color: #2563eb;">
            <div class="stat-value"><?php echo $pasien_hari_ini; ?></div>
            <div class="stat-label">Total Pasien Hari Ini</div>
        </div>
        
        <div class="stat-card" style="border-left-color: #f59e0b;">
            <div class="stat-value"><?php echo $pasien_menunggu; ?></div>
            <div class="stat-label">Pasien Menunggu</div>
        </div>
        
        <div class="stat-card" style="border-left-color: #10b981;">
            <div class="stat-value"><?php echo $pasien_selesai; ?></div>
            <div class="stat-label">Pasien Selesai Diperiksa</div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Menu Cepat</h2>
        </div>
        <a href="<?php echo base_url('pages/doctor/periksa.php'); ?>" class="btn btn-primary">
            🩺 Mulai Periksa Pasien
        </a>
    </div>
</main>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
