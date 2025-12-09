<?php
/**
 * Database Connection Configuration
 * Menggunakan PDO dengan prepared statements untuk keamanan
 */

// Konfigurasi database
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'simple_clinic');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Fungsi untuk mendapatkan koneksi database
function get_db_connection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_PERSISTENT         => false
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
        
    } catch (PDOException $e) {
        // Log error (jangan tampilkan detail ke user di production)
        error_log("Database Connection Error: " . $e->getMessage());
        die("Koneksi database gagal. Silakan hubungi administrator.");
    }
}

// Test koneksi (opsional, untuk development)
// $db = get_db_connection();
// echo "Database connected successfully!";
