<?php
$page_title = 'Data Dokter';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/helper.php';

check_role(['admin']);

// Ambil semua data dokter
try {
    $db = get_db_connection();
    $stmt = $db->query("SELECT * FROM doctors ORDER BY nama ASC");
    $doctors = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Get Doctors Error: " . $e->getMessage());
    $doctors = [];
}

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<main class="main-content">
    <div class="card">
        <div class="card-header flex justify-between items-center">
            <h1 class="card-title">Data Dokter</h1>
            <a href="#tambah" class="btn btn-primary">+ Tambah Dokter</a>
        </div>
        
        <?php if (empty($doctors)): ?>
            <p style="text-align: center; color: #64748b; padding: 2rem;">Belum ada data dokter.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Dokter</th>
                        <th>Spesialisasi</th>
                        <th>No. Telepon</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($doctors as $dokter): ?>
                    <tr>
                        <td>Dr. <?php echo clean_input($dokter['nama']); ?></td>
                        <td><?php echo clean_input($dokter['spesialisasi']); ?></td>
                        <td><?php echo clean_input($dokter['no_telepon']); ?></td>
                        <td><?php echo clean_input($dokter['email']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $dokter['status'] === 'aktif' ? 'success' : 'danger'; ?>">
                                <?php echo ucfirst($dokter['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-links">
                                <a href="#edit-<?php echo $dokter['id']; ?>" class="action-edit">Edit</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <!-- Form Tambah Dokter -->
    <div class="card" id="tambah">
        <div class="card-header">
            <h2 class="card-title">Tambah Dokter Baru</h2>
        </div>
        
        <form method="POST" action="<?php echo base_url('process/admin_add_dokter.php'); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            
            <div class="form-group">
                <label for="nama" class="form-label">Nama Lengkap *</label>
                <input type="text" id="nama" name="nama" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label for="spesialisasi" class="form-label">Spesialisasi *</label>
                <input type="text" id="spesialisasi" name="spesialisasi" class="form-input" required placeholder="Contoh: Umum, Gigi, Anak, dll">
            </div>
            
            <div class="form-group">
                <label for="no_telepon" class="form-label">No. Telepon *</label>
                <input type="text" id="no_telepon" name="no_telepon" class="form-input" required placeholder="08xxxx">
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-input" placeholder="dokter@email.com">
            </div>
            
            <div class="form-group">
                <label for="username" class="form-label">Username (untuk login) *</label>
                <input type="text" id="username" name="username" class="form-input" required>
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password *</label>
                <input type="password" id="password" name="password" class="form-input" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Simpan Data Dokter</button>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
