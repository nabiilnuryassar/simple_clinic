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
                                <form method="POST" action="<?php echo base_url('process/admin_delete_pasien.php'); ?>" style="display:inline;" onsubmit="return confirm('Yakin hapus pasien <?php echo htmlspecialchars($pasien['nama']); ?>?');">
                                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                    <input type="hidden" name="id" value="<?php echo $pasien['id']; ?>">
                                    <button type="submit" class="action-delete" style="border:none;background:none;cursor:pointer;color:#ef4444;padding:0;text-decoration:underline;">Hapus</button>
                                </form>
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
    
    <!-- Form Edit Pasien (untuk setiap pasien) -->
    <?php foreach ($patients as $pasien): ?>
    <div class="card" id="edit-<?php echo $pasien['id']; ?>">
        <div class="card-header">
            <h2 class="card-title">Edit Pasien: <?php echo clean_input($pasien['nama']); ?></h2>
        </div>
        
        <form method="POST" action="<?php echo base_url('process/admin_edit_pasien.php'); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="id" value="<?php echo $pasien['id']; ?>">
            
            <div class="form-group">
                <label class="form-label">No. Rekam Medis</label>
                <input type="text" class="form-input" value="<?php echo clean_input($pasien['no_rm']); ?>" disabled>
                <small style="color: #64748b;">Nomor RM tidak dapat diubah</small>
            </div>
            
            <div class="form-group">
                <label for="edit_nama_<?php echo $pasien['id']; ?>" class="form-label">Nama Lengkap *</label>
                <input type="text" id="edit_nama_<?php echo $pasien['id']; ?>" name="nama" class="form-input" value="<?php echo clean_input($pasien['nama']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="edit_tanggal_lahir_<?php echo $pasien['id']; ?>" class="form-label">Tanggal Lahir *</label>
                <input type="date" id="edit_tanggal_lahir_<?php echo $pasien['id']; ?>" name="tanggal_lahir" class="form-input" value="<?php echo $pasien['tanggal_lahir']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="edit_jenis_kelamin_<?php echo $pasien['id']; ?>" class="form-label">Jenis Kelamin *</label>
                <select id="edit_jenis_kelamin_<?php echo $pasien['id']; ?>" name="jenis_kelamin" class="form-select" required>
                    <option value="L" <?php echo $pasien['jenis_kelamin'] === 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                    <option value="P" <?php echo $pasien['jenis_kelamin'] === 'P' ? 'selected' : ''; ?>>Perempuan</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="edit_no_telepon_<?php echo $pasien['id']; ?>" class="form-label">No. Telepon</label>
                <input type="text" id="edit_no_telepon_<?php echo $pasien['id']; ?>" name="no_telepon" class="form-input" value="<?php echo clean_input($pasien['no_telepon']); ?>" placeholder="08xxxx">
            </div>
            
            <div class="form-group">
                <label for="edit_alamat_<?php echo $pasien['id']; ?>" class="form-label">Alamat *</label>
                <textarea id="edit_alamat_<?php echo $pasien['id']; ?>" name="alamat" class="form-textarea" required><?php echo clean_input($pasien['alamat']); ?></textarea>
            </div>
            
            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn btn-primary">Update Data</button>
                <a href="<?php echo base_url('pages/admin/pasien.php'); ?>" class="btn" style="background: #64748b;">Batal</a>
            </div>
        </form>
    </div>
    <?php endforeach; ?>
</main>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
