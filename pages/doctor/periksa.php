<?php
$page_title = 'Periksa Pasien';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/helper.php';

check_role(['dokter']);

// Ambil ID dokter yang sedang login
try {
    $db = get_db_connection();
    $stmt = $db->prepare("SELECT id FROM doctors WHERE nama = ? LIMIT 1");
    $stmt->execute([$_SESSION['nama']]);
    $doctor_data = $stmt->fetch();
    $doctor_id = $doctor_data['id'] ?? 0;
    
    // Ambil daftar pasien yang menunggu untuk dokter ini
    $stmt = $db->prepare("
        SELECT v.*, p.no_rm, p.nama as nama_pasien, p.tanggal_lahir, p.jenis_kelamin
        FROM visits v
        JOIN patients p ON v.patient_id = p.id
        WHERE v.doctor_id = ? 
        AND DATE(v.tanggal_kunjungan) = CURDATE() 
        AND v.status = 'menunggu'
        ORDER BY v.no_antrian ASC
    ");
    $stmt->execute([$doctor_id]);
    $waiting_patients = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Periksa Error: " . $e->getMessage());
    $waiting_patients = [];
    $doctor_id = 0;
}

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<main class="main-content">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Periksa Pasien - Antrian Hari Ini</h1>
        </div>
        
        <?php if (empty($waiting_patients)): ?>
            <p style="text-align: center; color: #64748b; padding: 2rem;">
                Tidak ada pasien yang menunggu saat ini. ✅
            </p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>No. Antrian</th>
                        <th>No. RM</th>
                        <th>Nama Pasien</th>
                        <th>Umur</th>
                        <th>Keluhan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($waiting_patients as $patient): 
                        $umur = date('Y') - date('Y', strtotime($patient['tanggal_lahir']));
                    ?>
                    <tr>
                        <td><strong><?php echo $patient['no_antrian']; ?></strong></td>
                        <td><?php echo clean_input($patient['no_rm']); ?></td>
                        <td><?php echo clean_input($patient['nama_pasien']); ?></td>
                        <td><?php echo $umur; ?> tahun (<?php echo $patient['jenis_kelamin'] === 'L' ? 'L' : 'P'; ?>)</td>
                        <td><?php echo clean_input($patient['keluhan']); ?></td>
                        <td>
                            <a href="#periksa-<?php echo $patient['id']; ?>" class="btn btn-primary btn-sm">
                                Periksa
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($waiting_patients)): 
        // Ambil pasien pertama untuk form
        $current_patient = $waiting_patients[0];
    ?>
    <!-- Form Pemeriksaan -->
    <div class="card" id="periksa-<?php echo $current_patient['id']; ?>">
        <div class="card-header">
            <h2 class="card-title">Form Pemeriksaan - Antrian #<?php echo $current_patient['no_antrian']; ?></h2>
        </div>
        
        <div style="background: #f8fafc; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
            <p><strong>Pasien:</strong> <?php echo clean_input($current_patient['nama_pasien']); ?> (<?php echo clean_input($current_patient['no_rm']); ?>)</p>
            <p><strong>Keluhan:</strong> <?php echo clean_input($current_patient['keluhan']); ?></p>
        </div>
        
        <form method="POST" action="<?php echo base_url('process/doctor_update_rm.php'); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="visit_id" value="<?php echo $current_patient['id']; ?>">
            <input type="hidden" name="patient_id" value="<?php echo $current_patient['patient_id']; ?>">
            
            <div class="form-group">
                <label for="anamnesa" class="form-label">Anamnesa *</label>
                <textarea id="anamnesa" name="anamnesa" class="form-textarea" required placeholder="Riwayat penyakit, keluhan detail"></textarea>
            </div>
            
            <div class="form-group">
                <label for="diagnosa" class="form-label">Diagnosa *</label>
                <textarea id="diagnosa" name="diagnosa" class="form-textarea" required placeholder="Hasil diagnosa penyakit"></textarea>
            </div>
            
            <div class="form-group">
                <label for="tindakan" class="form-label">Tindakan</label>
                <textarea id="tindakan" name="tindakan" class="form-textarea" placeholder="Tindakan medis yang dilakukan (opsional)"></textarea>
            </div>
            
            <div class="form-group">
                <label for="resep" class="form-label">Resep Obat</label>
                <textarea id="resep" name="resep" class="form-textarea" placeholder="Nama obat, dosis, aturan pakai (opsional)"></textarea>
            </div>
            
            <div class="form-group">
                <label for="catatan" class="form-label">Catatan Tambahan</label>
                <textarea id="catatan" name="catatan" class="form-textarea" placeholder="Catatan khusus (opsional)"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Simpan & Selesai Pemeriksaan</button>
        </form>
    </div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
