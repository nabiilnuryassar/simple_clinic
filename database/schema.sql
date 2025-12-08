-- =====================================================
-- DATABASE: simple_clinic
-- Sistem Informasi Klinik X - SQL Schema
-- =====================================================

-- Buat database (opsional, jika belum ada)
-- CREATE DATABASE IF NOT EXISTS simple_clinic;
-- USE simple_clinic;

-- =====================================================
-- TABEL: users (untuk autentikasi)
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    role ENUM('admin', 'dokter') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: patients (data pasien)
-- =====================================================
CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_rm VARCHAR(20) NOT NULL UNIQUE COMMENT 'Nomor Rekam Medis',
    nama VARCHAR(100) NOT NULL,
    tanggal_lahir DATE NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    no_telepon VARCHAR(15),
    alamat TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_no_rm (no_rm),
    INDEX idx_nama (nama)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: doctors (data dokter)
-- =====================================================
CREATE TABLE IF NOT EXISTS doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    spesialisasi VARCHAR(50) NOT NULL,
    no_telepon VARCHAR(15) NOT NULL,
    email VARCHAR(100),
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nama (nama),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: visits (kunjungan pasien)
-- =====================================================
CREATE TABLE IF NOT EXISTS visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    tanggal_kunjungan DATETIME NOT NULL,
    no_antrian INT NOT NULL,
    keluhan TEXT NOT NULL,
    status ENUM('menunggu', 'selesai', 'batal') DEFAULT 'menunggu',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    INDEX idx_tanggal (tanggal_kunjungan),
    INDEX idx_status (status),
    INDEX idx_patient (patient_id),
    INDEX idx_doctor (doctor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABEL: medical_records (rekam medis)
-- =====================================================
CREATE TABLE IF NOT EXISTS medical_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visit_id INT NOT NULL,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    tanggal_periksa DATETIME NOT NULL,
    anamnesa TEXT NOT NULL COMMENT 'Riwayat penyakit',
    diagnosa TEXT NOT NULL COMMENT 'Hasil diagnosa',
    tindakan TEXT COMMENT 'Tindakan medis',
    resep TEXT COMMENT 'Resep obat',
    catatan TEXT COMMENT 'Catatan tambahan',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (visit_id) REFERENCES visits(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    INDEX idx_patient (patient_id),
    INDEX idx_doctor (doctor_id),
    INDEX idx_tanggal (tanggal_periksa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DATA AWAL (SEEDING)
-- =====================================================

-- Insert user admin (username: admin, password: admin123)
INSERT INTO users (username, password, nama, role) VALUES
('admin', '$2y$10$DZc/PUgPtGibOyqpzHCm0efwDARhyIExMjGzGe5c0g37xFh.Ey562', 'Administrator Klinik', 'admin');
-- Password: admin123 (hashed with bcrypt)

-- Insert user dokter demo (username: dokter, password: dokter123)
INSERT INTO users (username, password, nama, role) VALUES
('dokter', '$2y$10$fM/H5Xpw7TZM2uilOW9E4u/t1TrCWhtY0IzfZeArQQtxdynKRrdqu', 'Dr. Ahmad Wijaya', 'dokter');
-- Password: dokter123 (hashed with bcrypt)

-- Insert dokter
INSERT INTO doctors (nama, spesialisasi, no_telepon, email, status) VALUES
('Dr. Ahmad Wijaya', 'Umum', '081234567890', 'ahmad@klinik.com', 'aktif'),
('Dr. Siti Nurhaliza', 'Anak', '081234567891', 'siti@klinik.com', 'aktif'),
('Dr. Budi Santoso', 'Gigi', '081234567892', 'budi@klinik.com', 'aktif');

-- Insert pasien contoh
INSERT INTO patients (no_rm, nama, tanggal_lahir, jenis_kelamin, no_telepon, alamat) VALUES
('RM-20250101-001', 'Andi Susanto', '1990-05-15', 'L', '081234567893', 'Jl. Merdeka No. 10, Jakarta'),
('RM-20250101-002', 'Rina Wati', '1985-08-20', 'P', '081234567894', 'Jl. Sudirman No. 25, Bandung'),
('RM-20250101-003', 'Budi Cahyono', '2000-12-05', 'L', '081234567895', 'Jl. Diponegoro No. 5, Surabaya');

-- =====================================================
-- NOTES:
-- 1. Password hash di atas adalah contoh. Gunakan PHP password_hash() untuk generate hash yang benar.
-- 2. Untuk generate password hash, jalankan di PHP:
--    echo password_hash('admin123', PASSWORD_BCRYPT);
--    echo password_hash('dokter123', PASSWORD_BCRYPT);
-- 3. Ganti hash di SQL dengan hasil dari PHP tersebut.
-- =====================================================
