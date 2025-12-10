# 📊 Analisis SQL: DDL dan DML - Simple Clinic Management System

> Dokumentasi lengkap penggunaan Data Definition Language (DDL) dan Data Manipulation Language (DML) dalam project Simple Clinic

---

## 📖 Daftar Isi

1. [Pengenalan DDL dan DML](#pengenalan-ddl-dan-dml)
2. [DDL (Data Definition Language)](#ddl-data-definition-language)
3. [DML (Data Manipulation Language)](#dml-data-manipulation-language)
4. [Ringkasan Penggunaan](#ringkasan-penggunaan)

---

## Pengenalan DDL dan DML

### Apa itu DDL?
**Data Definition Language (DDL)** adalah subset SQL yang digunakan untuk mendefinisikan dan memodifikasi struktur database, termasuk tabel, index, dan constraint.

**Perintah DDL utama:**
- `CREATE` - Membuat objek database (table, index, dll)
- `ALTER` - Mengubah struktur objek database
- `DROP` - Menghapus objek database
- `TRUNCATE` - Mengosongkan data tabel tanpa menghapus struktur

### Apa itu DML?
**Data Manipulation Language (DML)** adalah subset SQL yang digunakan untuk memanipulasi data dalam tabel database.

**Perintah DML utama:**
- `SELECT` - Mengambil/membaca data
- `INSERT` - Menambah data baru
- `UPDATE` - Mengubah data yang ada
- `DELETE` - Menghapus data

---

## DDL (Data Definition Language)

### 📍 Lokasi Implementasi
File: [`database/schema.sql`](../database/schema.sql)

### 1. CREATE TABLE

#### 1.1 Tabel `users`
```sql
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
```

**Tujuan:** Menyimpan data autentikasi user (admin & dokter)

**Struktur:**
- Primary Key: `id` (AUTO_INCREMENT)
- Unique Index: `username`
- Regular Index: `role`
- Constraint: `username` UNIQUE, `role` ENUM

---

#### 1.2 Tabel `patients`
```sql
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
```

**Tujuan:** Menyimpan data pasien klinik

**Struktur:**
- Primary Key: `id`
- Unique Index: `no_rm` (Nomor Rekam Medis)
- Regular Index: `nama`
- Constraint: `no_rm` UNIQUE, `jenis_kelamin` ENUM

---

#### 1.3 Tabel `doctors`
```sql
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
```

**Tujuan:** Menyimpan data dokter klinik

**Struktur:**
- Primary Key: `id`
- Regular Index: `nama`, `status`
- Constraint: `status` ENUM dengan default 'aktif'

---

#### 1.4 Tabel `visits`
```sql
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
```

**Tujuan:** Menyimpan data kunjungan/antrian pasien

**Struktur:**
- Primary Key: `id`
- Foreign Keys: 
  - `patient_id` → `patients(id)` ON DELETE CASCADE
  - `doctor_id` → `doctors(id)` ON DELETE CASCADE
- Regular Index: `tanggal_kunjungan`, `status`, `patient_id`, `doctor_id`
- Constraint: `status` ENUM dengan default 'menunggu'

**Relasi:** 
- Many-to-One dengan `patients`
- Many-to-One dengan `doctors`

---

#### 1.5 Tabel `medical_records`
```sql
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
```

**Tujuan:** Menyimpan rekam medis pemeriksaan pasien

**Struktur:**
- Primary Key: `id`
- Foreign Keys:
  - `visit_id` → `visits(id)` ON DELETE CASCADE
  - `patient_id` → `patients(id)` ON DELETE CASCADE
  - `doctor_id` → `doctors(id)` ON DELETE CASCADE
- Regular Index: `patient_id`, `doctor_id`, `tanggal_periksa`

**Relasi:**
- One-to-One dengan `visits`
- Many-to-One dengan `patients`
- Many-to-One dengan `doctors`

---

### 2. CREATE INDEX

Semua index dibuat inline saat CREATE TABLE:

| Tabel | Index Name | Kolom | Tipe |
|-------|-----------|-------|------|
| users | PRIMARY | id | PRIMARY KEY |
| users | username | username | UNIQUE |
| users | idx_username | username | INDEX |
| users | idx_role | role | INDEX |
| patients | PRIMARY | id | PRIMARY KEY |
| patients | no_rm | no_rm | UNIQUE |
| patients | idx_no_rm | no_rm | INDEX |
| patients | idx_nama | nama | INDEX |
| doctors | PRIMARY | id | PRIMARY KEY |
| doctors | idx_nama | nama | INDEX |
| doctors | idx_status | status | INDEX |
| visits | PRIMARY | id | PRIMARY KEY |
| visits | idx_tanggal | tanggal_kunjungan | INDEX |
| visits | idx_status | status | INDEX |
| visits | idx_patient | patient_id | INDEX |
| visits | idx_doctor | doctor_id | INDEX |
| medical_records | PRIMARY | id | PRIMARY KEY |
| medical_records | idx_patient | patient_id | INDEX |
| medical_records | idx_doctor | doctor_id | INDEX |
| medical_records | idx_tanggal | tanggal_periksa | INDEX |

**Alasan penggunaan index:**
- **PRIMARY KEY**: Identifikasi unik setiap row
- **UNIQUE**: Mencegah duplikasi (username, no_rm)
- **INDEX**: Mempercepat pencarian dan JOIN (nama, status, foreign keys)

---

### 3. FOREIGN KEY Constraints

| Tabel Child | Kolom | Referensi Parent | ON DELETE |
|-------------|-------|------------------|-----------|
| visits | patient_id | patients(id) | CASCADE |
| visits | doctor_id | doctors(id) | CASCADE |
| medical_records | visit_id | visits(id) | CASCADE |
| medical_records | patient_id | patients(id) | CASCADE |
| medical_records | doctor_id | doctors(id) | CASCADE |

**ON DELETE CASCADE:** Ketika parent record dihapus, child records otomatis ikut terhapus.

**Contoh:** Jika pasien dihapus → semua `visits` dan `medical_records` pasien tersebut otomatis terhapus.

---

## DML (Data Manipulation Language)

### 📍 Lokasi Implementasi
Tersebar di berbagai file PHP:
- `database/schema.sql` (seeding)
- `process/*.php` (CRUD operations)
- `pages/**/*.php` (read/display data)

---

### 1. INSERT (Menambah Data)

#### 1.1 Initial Data Seeding
**File:** [`database/schema.sql`](../database/schema.sql)

```sql
-- Insert user admin
INSERT INTO users (username, password, nama, role) 
VALUES ('admin', '$2y$10$...', 'Administrator Klinik', 'admin');

-- Insert user dokter
INSERT INTO users (username, password, nama, role) 
VALUES ('dokter', '$2y$10$...', 'Dr. Ahmad Wijaya', 'dokter');

-- Insert dokter
INSERT INTO doctors (nama, spesialisasi, no_telepon, email, status) 
VALUES 
    ('Dr. Ahmad Wijaya', 'Umum', '081234567890', 'ahmad@klinik.com', 'aktif'),
    ('Dr. Siti Nurhaliza', 'Anak', '081234567891', 'siti@klinik.com', 'aktif'),
    ('Dr. Budi Santoso', 'Gigi', '081234567892', 'budi@klinik.com', 'aktif');

-- Insert pasien
INSERT INTO patients (no_rm, nama, tanggal_lahir, jenis_kelamin, no_telepon, alamat) 
VALUES 
    ('RM-20250101-001', 'Andi Susanto', '1990-05-15', 'L', '081234567893', 'Jl. Merdeka No. 10'),
    ('RM-20250101-002', 'Rina Wati', '1985-08-20', 'P', '081234567894', 'Jl. Sudirman No. 25'),
    ('RM-20250101-003', 'Budi Cahyono', '2000-12-05', 'L', '081234567895', 'Jl. Diponegoro No. 5');
```

**Tujuan:** Mengisi data awal untuk testing dan demo

---

#### 1.2 Insert Pasien Baru
**File:** [`process/admin_add_pasien.php`](../process/admin_add_pasien.php)

```php
$stmt = $db->prepare("
    INSERT INTO patients (no_rm, nama, tanggal_lahir, jenis_kelamin, no_telepon, alamat, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, NOW())
");
$stmt->execute([$no_rm, $nama, $tanggal_lahir, $jenis_kelamin, $no_telepon, $alamat]);
```

**Flow:**
1. Generate nomor RM unik (format: RM-YYYYMMDD-XXX)
2. Insert data pasien dengan prepared statement (SQL Injection protection)
3. Return success/error message

**Keamanan:** Prepared statements + parameter binding

---

#### 1.3 Insert Dokter Baru (+ User Account)
**File:** [`process/admin_add_dokter.php`](../process/admin_add_dokter.php)

```php
// Insert ke tabel doctors
$stmt = $db->prepare("
    INSERT INTO doctors (nama, spesialisasi, no_telepon, email, status, created_at) 
    VALUES (?, ?, ?, ?, 'aktif', NOW())
");
$stmt->execute([$nama, $spesialisasi, $no_telepon, $email]);
$doctor_id = $db->lastInsertId();

// Insert ke tabel users (akun login dokter)
$stmt = $db->prepare("
    INSERT INTO users (username, password, nama, role, created_at) 
    VALUES (?, ?, ?, 'dokter', NOW())
");
$stmt->execute([$username, $hashed_password, $nama]);
```

**Flow:**
1. Validasi username belum dipakai
2. Hash password dengan bcrypt
3. Insert dokter → ambil `lastInsertId()`
4. Insert user dengan role='dokter'

**Keamanan:** Password hashing (bcrypt) + prepared statements

---

#### 1.4 Insert Kunjungan/Antrian
**File:** [`process/admin_add_visit.php`](../process/admin_add_visit.php)

```php
// Generate nomor antrian otomatis
$stmt = $db->prepare("
    SELECT MAX(no_antrian) as max_antrian 
    FROM visits 
    WHERE DATE(tanggal_kunjungan) = CURDATE()
");
$stmt->execute();
$result = $stmt->fetch();
$no_antrian = ($result['max_antrian'] ?? 0) + 1;

// Insert kunjungan baru
$stmt = $db->prepare("
    INSERT INTO visits (patient_id, doctor_id, tanggal_kunjungan, no_antrian, keluhan, status, created_at) 
    VALUES (?, ?, NOW(), ?, ?, 'menunggu', NOW())
");
$stmt->execute([$patient_id, $doctor_id, $no_antrian, $keluhan]);
```

**Flow:**
1. Hitung nomor antrian hari ini (MAX + 1)
2. Insert dengan status default 'menunggu'

---

#### 1.5 Insert Rekam Medis
**File:** [`process/doctor_update_rm.php`](../process/doctor_update_rm.php)

```php
// Insert rekam medis
$stmt = $db->prepare("
    INSERT INTO medical_records (visit_id, patient_id, doctor_id, anamnesa, diagnosa, tindakan, resep, catatan, tanggal_periksa, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
");
$stmt->execute([$visit_id, $patient_id, $doctor_id, $anamnesa, $diagnosa, $tindakan, $resep, $catatan]);
```

**Flow:**
1. Dokter input hasil pemeriksaan
2. Insert ke `medical_records`
3. Update status `visits` menjadi 'selesai'

---

### 2. SELECT (Membaca Data)

#### 2.1 Login Authentication
**File:** [`process/auth_login.php`](../process/auth_login.php)

```php
$stmt = $db->prepare("
    SELECT id, username, password, nama, role 
    FROM users 
    WHERE username = ? 
    LIMIT 1
");
$stmt->execute([$username]);
$user = $stmt->fetch();
```

**Tujuan:** Validasi kredensial login

---

#### 2.2 Dashboard Admin - Statistik
**File:** [`pages/admin/dashboard.php`](../pages/admin/dashboard.php)

```php
// Total pasien
$stmt = $db->query("SELECT COUNT(*) as total FROM patients");
$total_patients = $stmt->fetch()['total'];

// Total dokter
$stmt = $db->query("SELECT COUNT(*) as total FROM doctors");
$total_doctors = $stmt->fetch()['total'];

// Kunjungan hari ini
$stmt = $db->query("
    SELECT COUNT(*) as total 
    FROM visits 
    WHERE DATE(tanggal_kunjungan) = CURDATE()
");
$visits_today = $stmt->fetch()['total'];

// Total kunjungan
$stmt = $db->query("SELECT COUNT(*) as total FROM visits");
$total_visits = $stmt->fetch()['total'];
```

**Tujuan:** Menampilkan statistik overview klinik

---

#### 2.3 Dashboard Dokter - Statistik Personal
**File:** [`pages/doctor/dashboard.php`](../pages/doctor/dashboard.php)

```php
// Ambil doctor_id dari session atau cari berdasarkan nama
$doctor_id = $_SESSION['doctor_id'] ?? null;
if (empty($doctor_id)) {
    $stmt = $db->prepare("SELECT id FROM doctors WHERE nama = ? LIMIT 1");
    $stmt->execute([$_SESSION['nama']]);
    $doctor_id = $stmt->fetch()['id'] ?? 0;
}

// Pasien hari ini untuk dokter ini
$stmt = $db->prepare("
    SELECT COUNT(*) as total 
    FROM visits 
    WHERE doctor_id = ? AND DATE(tanggal_kunjungan) = CURDATE()
");
$stmt->execute([$doctor_id]);
$pasien_hari_ini = $stmt->fetch()['total'];

// Pasien menunggu
$stmt = $db->prepare("
    SELECT COUNT(*) as total 
    FROM visits 
    WHERE doctor_id = ? AND DATE(tanggal_kunjungan) = CURDATE() AND status = 'menunggu'
");
$stmt->execute([$doctor_id]);
$pasien_menunggu = $stmt->fetch()['total'];

// Pasien selesai diperiksa hari ini
$stmt = $db->prepare("
    SELECT COUNT(*) as total 
    FROM visits 
    WHERE doctor_id = ? AND DATE(tanggal_kunjungan) = CURDATE() AND status = 'selesai'
");
$stmt->execute([$doctor_id]);
$pasien_selesai = $stmt->fetch()['total'];
```

**Tujuan:** Statistik kunjungan pasien per dokter

---

#### 2.4 List Data - Pasien, Dokter, Antrian
**Files:** [`pages/admin/pasien.php`](../pages/admin/pasien.php), [`pages/admin/dokter.php`](../pages/admin/dokter.php), [`pages/admin/antrian.php`](../pages/admin/antrian.php)

```php
// Semua pasien
$stmt = $db->query("SELECT * FROM patients ORDER BY created_at DESC");
$patients = $stmt->fetchAll();

// Semua dokter
$stmt = $db->query("SELECT * FROM doctors ORDER BY nama ASC");
$doctors = $stmt->fetchAll();

// Antrian hari ini dengan JOIN
$stmt = $db->query("
    SELECT v.*, 
           p.no_rm, p.nama as nama_pasien,
           d.nama as nama_dokter
    FROM visits v
    JOIN patients p ON v.patient_id = p.id
    JOIN doctors d ON v.doctor_id = d.id
    WHERE DATE(v.tanggal_kunjungan) = CURDATE()
    ORDER BY v.no_antrian ASC
");
$visits = $stmt->fetchAll();
```

**Tujuan:** Menampilkan list data untuk CRUD operations

**Fitur JOIN:** Menggabungkan data dari multiple tables untuk display

---

#### 2.5 Dropdown Options
**File:** [`pages/admin/antrian.php`](../pages/admin/antrian.php)

```php
// Dropdown pasien
$stmt = $db->query("SELECT id, no_rm, nama FROM patients ORDER BY nama ASC");
$patients = $stmt->fetchAll();

// Dropdown dokter aktif
$stmt = $db->query("
    SELECT id, nama, spesialisasi 
    FROM doctors 
    WHERE status = 'aktif' 
    ORDER BY nama ASC
");
$doctors = $stmt->fetchAll();
```

**Tujuan:** Populate dropdown untuk form

---

#### 2.6 Antrian Pasien untuk Dokter
**File:** [`pages/doctor/periksa.php`](../pages/doctor/periksa.php)

```php
$stmt = $db->prepare("
    SELECT v.*, 
           p.no_rm, p.nama as nama_pasien, p.tanggal_lahir, p.jenis_kelamin, p.alamat
    FROM visits v
    JOIN patients p ON v.patient_id = p.id
    WHERE v.doctor_id = ? 
      AND DATE(v.tanggal_kunjungan) = CURDATE() 
      AND v.status = 'menunggu'
    ORDER BY v.no_antrian ASC
");
$stmt->execute([$doctor_id]);
$visits = $stmt->fetchAll();
```

**Tujuan:** Menampilkan antrian pasien yang menunggu untuk dokter tertentu

---

### 3. UPDATE (Mengubah Data)

#### 3.1 Update Pasien
**File:** [`process/admin_edit_pasien.php`](../process/admin_edit_pasien.php)

```php
$stmt = $db->prepare("
    UPDATE patients 
    SET nama = ?, 
        tanggal_lahir = ?, 
        jenis_kelamin = ?, 
        no_telepon = ?, 
        alamat = ?
    WHERE id = ?
");
$stmt->execute([$nama, $tanggal_lahir, $jenis_kelamin, $no_telepon, $alamat, $id]);
```

**Catatan:** `no_rm` tidak dapat diubah (disabled di form)

---

#### 3.2 Update Dokter (+ Sync User)
**File:** [`process/admin_edit_dokter.php`](../process/admin_edit_dokter.php)

```php
// Ambil data lama
$stmtOld = $db->prepare("SELECT * FROM doctors WHERE id = ? LIMIT 1");
$stmtOld->execute([$id]);
$old = $stmtOld->fetch();

// Update dokter
$stmt = $db->prepare("
    UPDATE doctors 
    SET nama = ?, 
        spesialisasi = ?, 
        no_telepon = ?, 
        email = ?,
        status = ?
    WHERE id = ?
");
$stmt->execute([$nama, $spesialisasi, $no_telepon, $email, $status, $id]);

// Sinkronisasi nama di tabel users (jika ada relasi)
if (!empty($old)) {
    if (isset($old['user_id']) && !empty($old['user_id'])) {
        $stmtUser = $db->prepare("UPDATE users SET nama = ? WHERE id = ? AND role = 'dokter'");
        $stmtUser->execute([$nama, $old['user_id']]);
    } else {
        $stmtUser = $db->prepare("UPDATE users SET nama = ? WHERE nama = ? AND role = 'dokter'");
        $stmtUser->execute([$nama, $old['nama']]);
    }
}
```

**Fitur khusus:** Sinkronisasi nama dokter ke tabel users untuk konsistensi

---

#### 3.3 Update Status Kunjungan
**File:** [`process/doctor_update_rm.php`](../process/doctor_update_rm.php)

```php
// Update status menjadi 'selesai' setelah input rekam medis
$stmt = $db->prepare("UPDATE visits SET status = 'selesai' WHERE id = ?");
$stmt->execute([$visit_id]);
```

**Flow:**
1. Dokter input rekam medis
2. Insert ke `medical_records`
3. Update status visit menjadi 'selesai'

---

### 4. DELETE (Menghapus Data)

#### 4.1 Delete Pasien
**File:** [`process/admin_delete_pasien.php`](../process/admin_delete_pasien.php)

```php
// Ambil nama sebelum dihapus (untuk flash message)
$stmt = $db->prepare("SELECT nama FROM patients WHERE id = ?");
$stmt->execute([$id]);
$pasien = $stmt->fetch();

// Hapus pasien (CASCADE akan hapus visits & medical_records otomatis)
$stmt = $db->prepare("DELETE FROM patients WHERE id = ?");
$stmt->execute([$id]);
```

**CASCADE Effect:**
- `visits` → Otomatis terhapus
- `medical_records` → Otomatis terhapus (via visits)

---

#### 4.2 Delete Dokter
**File:** [`process/admin_delete_dokter.php`](../process/admin_delete_dokter.php)

```php
// Ambil nama sebelum dihapus
$stmt = $db->prepare("SELECT nama FROM doctors WHERE id = ?");
$stmt->execute([$id]);
$dokter = $stmt->fetch();

// Hapus dokter (CASCADE akan hapus visits & medical_records otomatis)
$stmt = $db->prepare("DELETE FROM doctors WHERE id = ?");
$stmt->execute([$id]);
```

**CASCADE Effect:**
- `visits` → Otomatis terhapus
- `medical_records` → Otomatis terhapus (via visits)

---

#### 4.3 Delete Antrian/Kunjungan
**File:** [`process/admin_delete_visit.php`](../process/admin_delete_visit.php)

```php
// Ambil info antrian sebelum dihapus
$stmt = $db->prepare("
    SELECT v.no_antrian, p.nama as pasien_nama 
    FROM visits v
    JOIN patients p ON v.patient_id = p.id
    WHERE v.id = ?
");
$stmt->execute([$id]);
$visit = $stmt->fetch();

// Hapus antrian (CASCADE akan hapus medical_records otomatis)
$stmt = $db->prepare("DELETE FROM visits WHERE id = ?");
$stmt->execute([$id]);
```

**Batasan:** Hanya antrian dengan status 'menunggu' yang bisa dihapus (logic di UI)

**CASCADE Effect:**
- `medical_records` → Otomatis terhapus (jika ada)

---

## Ringkasan Penggunaan

### DDL Summary

| Statement | Jumlah | Keterangan |
|-----------|--------|------------|
| CREATE TABLE | 5 | users, patients, doctors, visits, medical_records |
| CREATE INDEX | 18 | PRIMARY, UNIQUE, dan INDEX biasa |
| FOREIGN KEY | 5 | Relasi antar tabel dengan CASCADE |
| ENUM | 4 | role, jenis_kelamin, status (dokter), status (visit) |

**Total objek database:**
- 5 Tables
- 18 Indexes (termasuk PRIMARY dan UNIQUE)
- 5 Foreign Key Constraints
- 4 ENUM types

---

### DML Summary

#### INSERT Operations

| Lokasi | Tabel | Fungsi |
|--------|-------|--------|
| `database/schema.sql` | users, doctors, patients | Initial seeding |
| `process/admin_add_pasien.php` | patients | Tambah pasien baru |
| `process/admin_add_dokter.php` | doctors, users | Tambah dokter + akun |
| `process/admin_add_visit.php` | visits | Tambah antrian |
| `process/doctor_update_rm.php` | medical_records | Input rekam medis |

**Total: 5 INSERT locations**

---

#### SELECT Operations

| Lokasi | Tujuan | Kompleksitas |
|--------|--------|--------------|
| `process/auth_login.php` | Login validation | Simple WHERE |
| `pages/admin/dashboard.php` | Statistik overview | COUNT + DATE functions |
| `pages/doctor/dashboard.php` | Statistik dokter | COUNT + WHERE + DATE |
| `pages/admin/pasien.php` | List pasien | SELECT * + ORDER BY |
| `pages/admin/dokter.php` | List dokter | SELECT * + ORDER BY |
| `pages/admin/antrian.php` | List antrian | JOIN 3 tables |
| `pages/doctor/periksa.php` | Antrian dokter | JOIN + WHERE + DATE |

**Total: 15+ SELECT queries**

**Teknik yang digunakan:**
- Simple SELECT
- COUNT() aggregate
- DATE() functions (CURDATE, DATE filtering)
- INNER JOIN (2-3 tables)
- WHERE conditions
- ORDER BY sorting
- LIMIT pagination

---

#### UPDATE Operations

| Lokasi | Tabel | Keterangan |
|--------|-------|------------|
| `process/admin_edit_pasien.php` | patients | Update data pasien |
| `process/admin_edit_dokter.php` | doctors, users | Update dokter + sync nama |
| `process/doctor_update_rm.php` | visits | Update status menjadi 'selesai' |

**Total: 3 UPDATE locations (4 queries)**

**Fitur khusus:** Sinkronisasi nama dokter ke tabel users untuk konsistensi data

---

#### DELETE Operations

| Lokasi | Tabel | CASCADE Effect |
|--------|-------|----------------|
| `process/admin_delete_pasien.php` | patients | visits, medical_records |
| `process/admin_delete_dokter.php` | doctors | visits, medical_records |
| `process/admin_delete_visit.php` | visits | medical_records |

**Total: 3 DELETE locations**

**Keamanan:** CASCADE DELETE memastikan tidak ada orphaned records

---

### Keamanan SQL

#### 1. SQL Injection Protection
✅ **Semua query menggunakan Prepared Statements**

```php
// ❌ VULNERABLE (tidak dipakai di project ini)
$query = "SELECT * FROM users WHERE username = '$username'";

// ✅ SECURE (yang dipakai)
$stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
```

#### 2. Password Security
✅ **Bcrypt hashing untuk semua password**

```php
$hashed = password_hash($password, PASSWORD_BCRYPT);
$is_valid = password_verify($password, $hashed);
```

#### 3. CSRF Protection
✅ **Token validation di semua form POST**

```php
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    die('Invalid CSRF token');
}
```

#### 4. Input Sanitization
✅ **XSS protection dengan `htmlspecialchars()`**

```php
function clean_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
```

---

### Best Practices yang Diterapkan

1. ✅ **Prepared Statements** - Semua query parametrized
2. ✅ **Foreign Key Constraints** - Referential integrity
3. ✅ **CASCADE DELETE** - Prevent orphaned records
4. ✅ **Indexes** - Performance optimization
5. ✅ **ENUM types** - Data validation di DB level
6. ✅ **Timestamps** - Audit trail (created_at, updated_at)
7. ✅ **InnoDB Engine** - ACID compliance & transactions
8. ✅ **UTF-8 Charset** - Internationalization support
9. ✅ **Error Logging** - `error_log()` untuk debugging
10. ✅ **PDO Fetch Modes** - Type-safe data retrieval

---

## 📚 Referensi

- [MySQL Documentation - DDL Statements](https://dev.mysql.com/doc/refman/8.0/en/sql-data-definition-statements.html)
- [MySQL Documentation - DML Statements](https://dev.mysql.com/doc/refman/8.0/en/sql-data-manipulation-statements.html)
- [PHP PDO Documentation](https://www.php.net/manual/en/book.pdo.php)
- [OWASP SQL Injection Prevention](https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html)

---

**Last Updated:** December 10, 2025  
**Project:** Simple Clinic Management System  
**Version:** 2.0.0
