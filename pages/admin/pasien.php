<?php
$page_title = 'Data Pasien';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/helper.php';

check_role(['admin']);

// Ambil semua data pasien
try {
    $db = get_db_connection();
    $stmt = $db->query("SELECT * FROM patients ORDER BY created_at DESC");
    $patients = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Get Patients Error: " . $e->getMessage());
    $patients = [];
}

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<main class="main-content">
    <div class="card">
        <div class="card-header flex justify-between items-center">
            <h1 class="card-title">Data Pasien</h1>
            <a href="#tambah" class="btn btn-primary">+ Tambah Pasien</a>
        </div>
        
        <?php if (empty($patients)): ?>
            <p style="text-align: center; color: #64748b; padding: 2rem;">Belum ada data pasien.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>No. RM</th>
                        <th>Nama Pasien</th>
                        <th>Tanggal Lahir</th>
                        <th>Jenis Kelamin</th>
                        <th>No. Telepon</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patients as $pasien): ?>
                    <tr>
                        <td><?php echo clean_input($pasien['no_rm']); ?></td>
                        <td><?php echo clean_input($pasien['nama']); ?></td>
                        <td><?php echo format_date($pasien['tanggal_lahir']); ?></td>
                        <td><?php echo $pasien['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
                        <td><?php echo clean_input($pasien['no_telepon']); ?></td>
                        <td><?php echo clean_input($pasien['alamat']); ?></td>
                        <td>
                            <div class="action-links">
                                <a href="#edit-<?php echo $pasien['id']; ?>" class="action-edit">Edit</a>
                                <a href="#delete-<?php echo $pasien['id']; ?>" class="action-delete">Hapus</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <!-- Form Tambah Pasien -->
    <div class="card" id="tambah">
        <div class="card-header">
            <h2 class="card-title">Tambah Pasien Baru</h2>
        </div>
        
        <form method="POST" action="<?php echo base_url('process/admin_add_pasien.php'); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            
            <div class="form-group">
                <label for="nama" class="form-label">Nama Lengkap *</label>
                <input type="text" id="nama" name="nama" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label for="tanggal_lahir" class="form-label">Tanggal Lahir *</label>
                <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label for="jenis_kelamin" class="form-label">Jenis Kelamin *</label>
                <select id="jenis_kelamin" name="jenis_kelamin" class="form-select" required>
                    <option value="">-- Pilih --</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="no_telepon" class="form-label">No. Telepon</label>
                <input type="text" id="no_telepon" name="no_telepon" class="form-input" placeholder="08xxxx">
            </div>
            
            <div class="form-group">
                <label for="alamat" class="form-label">Alamat *</label>
                <textarea id="alamat" name="alamat" class="form-textarea" required></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Simpan Data Pasien</button>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
