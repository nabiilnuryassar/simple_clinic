<?php
/**
 * Generate Password Hash
 * Script untuk generate bcrypt hash untuk password
 * Jalankan sekali untuk mendapatkan hash yang benar
 */

echo "========================================\n";
echo " PASSWORD HASH GENERATOR\n";
echo " Klinik X - Sistem Informasi Klinik\n";
echo "========================================\n\n";

// Password yang ingin di-hash
$passwords = [
    'admin123'  => 'Admin',
    'dokter123' => 'Dokter'
];

foreach ($passwords as $password => $role) {
    $hash = password_hash($password, PASSWORD_BCRYPT);
    echo "{$role} Password: {$password}\n";
    echo "Hash: {$hash}\n\n";
}

echo "========================================\n";
echo "Salin hash di atas ke database/schema.sql\n";
echo "Ganti nilai password di INSERT INTO users\n";
echo "========================================\n";
