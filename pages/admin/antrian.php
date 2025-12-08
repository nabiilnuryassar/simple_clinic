<?php
$page_title = 'Antrian Kunjungan';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/helper.php';

check_role(['admin']);

// Ambil data kunjungan hari ini
try {
    $db = get_db_connection();
    $stmt = $db->query("
        SELECT v.*, p.no_rm, p.nama as nama_pasien, d.nama as nama_dokter
        FROM visits v
        JOIN patients p ON v.patient_id = p.id
        JOIN doctors d ON v.doctor_id = d.id
        WHERE DATE(v.tanggal_kunjungan) = CURDATE()
        ORDER BY v.no_antrian ASC
    ");
    $visits = $stmt->fetchAll();
    
    // Ambil daftar pasien untuk form
    $stmt = $db->query("SELECT id, no_rm, nama FROM patients ORDER BY nama ASC");
    $patients = $stmt->fetchAll();
    
    // Ambil daftar dokter aktif untuk form
    $stmt = $db->query("SELECT id, nama, spesialisasi FROM doctors WHERE status = 'aktif' ORDER BY nama ASC");
    $doctors = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Get Visits Error: " . $e->getMessage());
    $visits = $patients = $doctors = [];
}

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<main class="main-content">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Antrian Kunjungan Hari Ini - <?php echo format_date(date('Y-m-d')); ?></h1>
        </div>
        
        <?php if (empty($visits)): ?>
            <p style="text-align: center; color: #64748b; padding: 2rem;">Belum ada antrian hari ini.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>No. Antrian</th>
                        <th>No. RM</th>
                        <th>Nama Pasien</th>
                        <th>Dokter</th>
                        <th>Keluhan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($visits as $visit): ?>
                    <tr>
                        <td><strong><?php echo $visit['no_antrian']; ?></strong></td>
                        <td><?php echo clean_input($visit['no_rm']); ?></td>
                        <td><?php echo clean_input($visit['nama_pasien']); ?></td>
                        <td>Dr. <?php echo clean_input($visit['nama_dokter']); ?></td>
                        <td><?php echo clean_input($visit['keluhan']); ?></td>
                        <td>
                            <?php
                            $status_class = 'badge-warning';
                            if ($visit['status'] === 'selesai') $status_class = 'badge-success';
                            if ($visit['status'] === 'batal') $status_class = 'badge-danger';
                            ?>
                            <span class="badge <?php echo $status_class; ?>">
                                <?php echo ucfirst($visit['status']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <!-- Form Buat Antrian Baru -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Buat Antrian Kunjungan Baru</h2>
        </div>
        
        <?php if (empty($patients) || empty($doctors)): ?>
            <p style="color: #ef4444;">Tidak bisa membuat antrian. Pastikan sudah ada data pasien dan dokter.</p>
        <?php else: ?>
        <form method="POST" action="<?php echo base_url('process/admin_add_visit.php'); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            
            <div class="form-group">
                <label for="patient_id" class="form-label">Pilih Pasien *</label>
                <select id="patient_id" name="patient_id" class="form-select" required>
                    <option value="">-- Pilih Pasien --</option>
                    <?php foreach ($patients as $pasien): ?>
                        <option value="<?php echo $pasien['id']; ?>">
                            <?php echo clean_input($pasien['no_rm']) . ' - ' . clean_input($pasien['nama']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="doctor_id" class="form-label">Pilih Dokter *</label>
                <select id="doctor_id" name="doctor_id" class="form-select" required>
                    <option value="">-- Pilih Dokter --</option>
                    <?php foreach ($doctors as $dokter): ?>
                        <option value="<?php echo $dokter['id']; ?>">
                            Dr. <?php echo clean_input($dokter['nama']) . ' (' . clean_input($dokter['spesialisasi']) . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="keluhan" class="form-label">Keluhan Utama *</label>
                <textarea id="keluhan" name="keluhan" class="form-textarea" required placeholder="Deskripsikan keluhan pasien"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Buat Antrian</button>
        </form>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
