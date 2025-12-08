<?php
$page_title = 'Login';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/helper.php';

// Jika sudah login, redirect ke dashboard
if (is_logged_in()) {
    if ($_SESSION['role'] === 'admin') {
        redirect('pages/admin/dashboard.php');
    } else {
        redirect('pages/doctor/dashboard.php');
    }
}

require_once __DIR__ . '/../../layout/header.php';
?>

<div class="login-container">
    <div class="login-card">
        <h1 class="login-title">🏥 Login Klinik X</h1>
        
        <form method="POST" action="<?php echo base_url('process/auth_login.php'); ?>">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            
            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       class="form-input" 
                       required 
                       autofocus
                       placeholder="Masukkan username">
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       class="form-input" 
                       required
                       placeholder="Masukkan password">
            </div>
            
            <button type="submit" class="btn btn-primary w-full">Login</button>
        </form>
        
        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e2e8f0; text-align: center; color: #64748b; font-size: 0.875rem;">
            <p>Demo Login:</p>
            <p><strong>Admin:</strong> admin / admin123</p>
            <p><strong>Dokter:</strong> dokter / dokter123</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
