# 🗄️ Database Structure Documentation - Sistem Informasi Klinik Mutiara

Dokumentasi lengkap struktur database, relasi antar tabel, dan penjelasan fungsi setiap kolom.

---

## 📋 Table of Contents

1. [Overview Database](#overview-database)
2. [Entity Relationship Diagram](#entity-relationship-diagram)
3. [Table Structures](#table-structures)
4. [Relationships & Foreign Keys](#relationships--foreign-keys)
5. [Indexes & Performance](#indexes--performance)
6. [Data Types Explanation](#data-types-explanation)
7. [Seed Data](#seed-data)
8. [Query Examples](#query-examples)

---

## 🎯 Overview Database

### Database Information

| Property | Value |
|----------|-------|
| **Database Name** | `simple_clinic` |
| **Character Set** | `utf8mb4` |
| **Collation** | `utf8mb4_unicode_ci` |
| **Engine** | InnoDB (Support transactions & foreign keys) |
| **Total Tables** | 5 tables |

### Purpose

Database ini dirancang untuk mengelola:
- ✅ User authentication (admin & dokter)
- ✅ Data master pasien
- ✅ Data master dokter
- ✅ Antrian kunjungan pasien
- ✅ Rekam medis pemeriksaan

---

## 📊 Entity Relationship Diagram

```
┌─────────────┐
│    users    │ (Authentication)
│  ─────────  │
│  - id (PK)  │
│  - username │
│  - password │
│  - nama     │
│  - role     │
└─────────────┘

┌──────────────┐         ┌──────────────┐
│   patients   │         │   doctors    │
│  ──────────  │         │  ──────────  │
│  - id (PK)   │         │  - id (PK)   │
│  - no_rm     │         │  - nama      │
│  - nama      │         │  - spesialisasi│
│  - tgl_lahir │         │  - no_telp   │
│  - kelamin   │         │  - email     │
│  - alamat    │         │  - status    │
└──────┬───────┘         └──────┬───────┘
       │                        │
       │    ┌──────────────┐    │
       └───►│    visits    │◄───┘
            │  ──────────  │
            │  - id (PK)   │
            │  - patient_id (FK)│
            │  - doctor_id (FK) │
            │  - tgl_kunjungan  │
            │  - no_antrian│
            │  - keluhan   │
            │  - status    │
            └──────┬───────┘
                   │
                   │
            ┌──────▼──────────────┐
            │  medical_records    │
            │  ─────────────────  │
            │  - id (PK)          │
            │  - visit_id (FK)    │
            │  - patient_id (FK)  │
            │  - doctor_id (FK)   │
            │  - anamnesa         │
            │  - diagnosa         │
            │  - tindakan         │
            │  - resep            │
            └─────────────────────┘
```

**Relasi:**
- `patients` → `visits` (One-to-Many)
- `doctors` → `visits` (One-to-Many)
- `visits` → `medical_records` (One-to-One)

---

## 📋 Table Structures

### 1. Table: `users`

**Purpose:** Menyimpan data user untuk authentication (Admin & Dokter)

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | INT | PK, AUTO_INCREMENT | ID unik user |
| `username` | VARCHAR(50) | NOT NULL, UNIQUE | Username login (unique) |
| `password` | VARCHAR(255) | NOT NULL | Password hash (bcrypt) |
| `nama` | VARCHAR(100) | NOT NULL | Nama lengkap user |
| `role` | ENUM('admin','dokter') | NOT NULL | Role user (admin/dokter) |
| `created_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP | Waktu pembuatan record |
| `updated_at` | DATETIME | ON UPDATE CURRENT_TIMESTAMP | Waktu update terakhir |

**Indexes:**
- `PRIMARY KEY` on `id`
- `INDEX idx_username` on `username` (untuk speed up login query)
- `INDEX idx_role` on `role` (untuk filter by role)

**Business Rules:**
- ✅ Username harus unique (tidak boleh duplicate)
- ✅ Password harus di-hash dengan bcrypt (min 60 karakter)
- ✅ Role hanya boleh 'admin' atau 'dokter'
- ⚠️ Tidak ada soft delete - jika user dihapus, hilang permanent

**Example Data:**
```sql
id: 1
username: admin
password: $2y$10$DZc/PUgPtGibOyqpzHCm0e... (bcrypt hash)
nama: Administrator Klinik
role: admin
```

---

### 2. Table: `patients`

**Purpose:** Menyimpan data master pasien klinik

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | INT | PK, AUTO_INCREMENT | ID unik pasien |
| `no_rm` | VARCHAR(20) | NOT NULL, UNIQUE | Nomor Rekam Medis (unique) |
| `nama` | VARCHAR(100) | NOT NULL | Nama lengkap pasien |
| `tanggal_lahir` | DATE | NOT NULL | Tanggal lahir pasien |
| `jenis_kelamin` | ENUM('L','P') | NOT NULL | Jenis kelamin (L=Laki, P=Perempuan) |
| `no_telepon` | VARCHAR(15) | NULL | Nomor telepon/HP |
| `alamat` | TEXT | NOT NULL | Alamat lengkap pasien |
| `created_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP | Waktu pendaftaran |
| `updated_at` | DATETIME | ON UPDATE CURRENT_TIMESTAMP | Waktu update terakhir |

**Indexes:**
- `PRIMARY KEY` on `id`
- `UNIQUE INDEX` on `no_rm` (mencegah duplikasi RM)
- `INDEX idx_nama` on `nama` (untuk search by name)

**Business Rules:**
- ✅ Nomor RM auto-generate dengan format: `RM-YYYYMMDD-XXX`
- ✅ Nomor RM harus unique (satu pasien = satu RM selamanya)
- ✅ Jenis kelamin hanya 'L' atau 'P'
- ✅ Alamat wajib diisi (tidak boleh kosong)
- ⚠️ No telepon optional (bisa NULL)

**Example Data:**
```sql
id: 1
no_rm: RM-20250101-001
nama: Andi Susanto
tanggal_lahir: 1990-05-15
jenis_kelamin: L
no_telepon: 081234567893
alamat: Jl. Merdeka No. 10, Jakarta
```

---

### 3. Table: `doctors`

**Purpose:** Menyimpan data master dokter

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | INT | PK, AUTO_INCREMENT | ID unik dokter |
| `nama` | VARCHAR(100) | NOT NULL | Nama lengkap dokter |
| `spesialisasi` | VARCHAR(50) | NOT NULL | Spesialisasi dokter (Umum, Anak, Gigi, dll) |
| `no_telepon` | VARCHAR(15) | NOT NULL | Nomor telepon dokter |
| `email` | VARCHAR(100) | NULL | Email dokter (optional) |
| `status` | ENUM('aktif','nonaktif') | DEFAULT 'aktif' | Status dokter (aktif/nonaktif) |
| `created_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP | Waktu pendaftaran |
| `updated_at` | DATETIME | ON UPDATE CURRENT_TIMESTAMP | Waktu update terakhir |

**Indexes:**
- `PRIMARY KEY` on `id`
- `INDEX idx_nama` on `nama` (untuk search dokter)
- `INDEX idx_status` on `status` (untuk filter dokter aktif)

**Business Rules:**
- ✅ Status default adalah 'aktif'
- ✅ Hanya dokter 'aktif' yang bisa dipilih untuk kunjungan baru
- ✅ Dokter 'nonaktif' tidak bisa menerima pasien baru
- ⚠️ Tidak ada soft delete - data dokter historis tetap ada untuk referensi

**Example Data:**
```sql
id: 1
nama: Dr. Ahmad Wijaya
spesialisasi: Umum
no_telepon: 081234567890
email: ahmad@klinik.com
status: aktif
```

---

### 4. Table: `visits`

**Purpose:** Menyimpan data antrian kunjungan pasien ke dokter

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | INT | PK, AUTO_INCREMENT | ID unik kunjungan |
| `patient_id` | INT | FK, NOT NULL | ID pasien (relasi ke table patients) |
| `doctor_id` | INT | FK, NOT NULL | ID dokter (relasi ke table doctors) |
| `tanggal_kunjungan` | DATETIME | NOT NULL | Tanggal & jam kunjungan |
| `no_antrian` | INT | NOT NULL | Nomor antrian hari itu |
| `keluhan` | TEXT | NOT NULL | Keluhan utama pasien |
| `status` | ENUM('menunggu','selesai','batal') | DEFAULT 'menunggu' | Status kunjungan |
| `created_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP | Waktu pembuatan antrian |
| `updated_at` | DATETIME | ON UPDATE CURRENT_TIMESTAMP | Waktu update terakhir |

**Indexes:**
- `PRIMARY KEY` on `id`
- `FOREIGN KEY` on `patient_id` → `patients(id)` (CASCADE DELETE)
- `FOREIGN KEY` on `doctor_id` → `doctors(id)` (CASCADE DELETE)
- `INDEX idx_tanggal` on `tanggal_kunjungan`
- `INDEX idx_status` on `status`
- `INDEX idx_patient` on `patient_id`
- `INDEX idx_doctor` on `doctor_id`

**Business Rules:**
- ✅ Satu pasien bisa punya banyak kunjungan
- ✅ Satu dokter bisa menerima banyak pasien
- ✅ Status default adalah 'menunggu'
- ✅ Setelah diperiksa, status berubah menjadi 'selesai'
- ✅ Jika pasien/dokter dihapus, kunjungan juga terhapus (CASCADE)
- ⚠️ No antrian di-reset setiap hari

**Status Flow:**
```
menunggu → selesai (normal flow)
menunggu → batal (jika pasien tidak datang)
```

**Example Data:**
```sql
id: 1
patient_id: 1 (Andi Susanto)
doctor_id: 1 (Dr. Ahmad Wijaya)
tanggal_kunjungan: 2025-12-08 09:00:00
no_antrian: 1
keluhan: Demam dan batuk sejak 3 hari
status: menunggu
```

---

### 5. Table: `medical_records`

**Purpose:** Menyimpan rekam medis hasil pemeriksaan dokter

| Column | Type | Constraint | Description |
|--------|------|------------|-------------|
| `id` | INT | PK, AUTO_INCREMENT | ID unik rekam medis |
| `visit_id` | INT | FK, NOT NULL | ID kunjungan (relasi ke visits) |
| `patient_id` | INT | FK, NOT NULL | ID pasien (relasi ke patients) |
| `doctor_id` | INT | FK, NOT NULL | ID dokter (relasi ke doctors) |
| `tanggal_periksa` | DATETIME | NOT NULL | Tanggal & jam pemeriksaan |
| `anamnesa` | TEXT | NOT NULL | Anamnesa (riwayat penyakit dari pasien) |
| `diagnosa` | TEXT | NOT NULL | Diagnosa hasil pemeriksaan dokter |
| `tindakan` | TEXT | NULL | Tindakan medis yang dilakukan (optional) |
| `resep` | TEXT | NULL | Resep obat (optional) |
| `catatan` | TEXT | NULL | Catatan tambahan dokter (optional) |
| `created_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP | Waktu pembuatan rekam medis |
| `updated_at` | DATETIME | ON UPDATE CURRENT_TIMESTAMP | Waktu update terakhir |

**Indexes:**
- `PRIMARY KEY` on `id`
- `FOREIGN KEY` on `visit_id` → `visits(id)` (CASCADE DELETE)
- `FOREIGN KEY` on `patient_id` → `patients(id)` (CASCADE DELETE)
- `FOREIGN KEY` on `doctor_id` → `doctors(id)` (CASCADE DELETE)
- `INDEX idx_patient` on `patient_id`
- `INDEX idx_doctor` on `doctor_id`
- `INDEX idx_tanggal` on `tanggal_periksa`

**Business Rules:**
- ✅ Satu kunjungan = satu rekam medis (One-to-One)
- ✅ Anamnesa & diagnosa wajib diisi
- ✅ Tindakan, resep, catatan optional (bisa NULL)
- ✅ Jika visit dihapus, rekam medis juga terhapus (CASCADE)
- ⚠️ Rekam medis adalah data sensitif (proteksi tinggi!)

**Example Data:**
```sql
id: 1
visit_id: 1
patient_id: 1
doctor_id: 1
tanggal_periksa: 2025-12-08 09:15:00
anamnesa: Pasien mengeluh demam 38.5°C, batuk berdahak, sakit kepala
diagnosa: ISPA (Infeksi Saluran Pernafasan Atas)
tindakan: Pemeriksaan fisik, pengukuran suhu, tekanan darah
resep: Paracetamol 3x500mg, Amoxicillin 3x500mg
catatan: Istirahat cukup, banyak minum air putih, kontrol 3 hari lagi
```

---

## 🔗 Relationships & Foreign Keys

### Relationship Details

#### 1. patients → visits (One-to-Many)
```sql
FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
```
- **Meaning:** Satu pasien bisa punya banyak kunjungan
- **CASCADE DELETE:** Jika pasien dihapus, semua kunjungannya juga terhapus

#### 2. doctors → visits (One-to-Many)
```sql
FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
```
- **Meaning:** Satu dokter bisa menerima banyak pasien
- **CASCADE DELETE:** Jika dokter dihapus, semua kunjungan ke dokter itu terhapus

#### 3. visits → medical_records (One-to-One)
```sql
FOREIGN KEY (visit_id) REFERENCES visits(id) ON DELETE CASCADE
```
- **Meaning:** Satu kunjungan menghasilkan satu rekam medis
- **CASCADE DELETE:** Jika kunjungan dihapus, rekam medisnya juga terhapus

#### 4. patients → medical_records (One-to-Many)
```sql
FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
```
- **Meaning:** Satu pasien punya banyak rekam medis (dari berbagai kunjungan)
- **CASCADE DELETE:** Jika pasien dihapus, semua rekam medisnya terhapus

#### 5. doctors → medical_records (One-to-Many)
```sql
FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
```
- **Meaning:** Satu dokter membuat banyak rekam medis
- **CASCADE DELETE:** Jika dokter dihapus, semua rekam medis yang dibuatnya terhapus

### Cascade Delete Flow

Contoh: Jika `patients(id=1)` dihapus:
```
DELETE FROM patients WHERE id = 1;
    ↓
Otomatis menghapus:
- visits(patient_id=1) → Semua kunjungan pasien ini
    ↓
- medical_records(patient_id=1) → Semua rekam medis pasien ini
- medical_records(visit_id=X) → Rekam medis dari kunjungan yang terhapus
```

---

## 🚀 Indexes & Performance

### Purpose of Indexes

Index mempercepat query dengan membuat "pointer" ke data.

### Index Strategy

| Table | Index | Type | Purpose |
|-------|-------|------|---------|
| **users** | `id` | PRIMARY | Unique identifier |
| | `username` | INDEX | Speed up login query |
| | `role` | INDEX | Filter by role (admin/dokter) |
| **patients** | `id` | PRIMARY | Unique identifier |
| | `no_rm` | UNIQUE | Prevent duplicate RM |
| | `nama` | INDEX | Search patient by name |
| **doctors** | `id` | PRIMARY | Unique identifier |
| | `nama` | INDEX | Search doctor by name |
| | `status` | INDEX | Filter active doctors |
| **visits** | `id` | PRIMARY | Unique identifier |
| | `patient_id` | INDEX + FK | Join with patients |
| | `doctor_id` | INDEX + FK | Join with doctors |
| | `tanggal_kunjungan` | INDEX | Filter by date |
| | `status` | INDEX | Filter by status |
| **medical_records** | `id` | PRIMARY | Unique identifier |
| | `visit_id` | INDEX + FK | Join with visits |
| | `patient_id` | INDEX + FK | History per patient |
| | `doctor_id` | INDEX + FK | History per doctor |
| | `tanggal_periksa` | INDEX | Filter by date |

### Query Performance Examples

**Tanpa Index:**
```sql
SELECT * FROM patients WHERE nama = 'Andi'; 
-- Scan 10,000 rows (slow)
```

**Dengan Index:**
```sql
SELECT * FROM patients WHERE nama = 'Andi'; 
-- Use index: Only scan matching rows (fast)
```

---

## 📊 Data Types Explanation

### VARCHAR vs TEXT

| Type | Max Length | When to Use |
|------|------------|-------------|
| `VARCHAR(50)` | 50 characters | Username, short text |
| `VARCHAR(100)` | 100 characters | Nama, email |
| `TEXT` | 65,535 characters | Keluhan, diagnosa, alamat panjang |

### INT vs ENUM

| Type | Storage | When to Use |
|------|---------|-------------|
| `INT` | 4 bytes | ID, numbers, counters |
| `ENUM('L','P')` | 1 byte | Fixed options (gender, status) |

### DATE vs DATETIME

| Type | Format | When to Use |
|------|--------|-------------|
| `DATE` | YYYY-MM-DD | Tanggal lahir (no time) |
| `DATETIME` | YYYY-MM-DD HH:MM:SS | Kunjungan, pemeriksaan (with time) |

---

## 🌱 Seed Data

### Default Users

| Username | Password | Nama | Role |
|----------|----------|------|------|
| `admin` | `admin123` | Administrator Klinik | admin |
| `dokter` | `dokter123` | Dr. Ahmad Wijaya | dokter |

**⚠️ SECURITY WARNING:** Ganti password default di production!

### Default Doctors (3 records)

1. Dr. Ahmad Wijaya - Umum
2. Dr. Siti Nurhaliza - Anak
3. Dr. Budi Santoso - Gigi

### Default Patients (3 records)

1. Andi Susanto - RM-20250101-001
2. Rina Wati - RM-20250101-002
3. Budi Cahyono - RM-20250101-003

---

## 🔍 Query Examples

### Get Patient Full Info with Latest Visit

```sql
SELECT 
    p.no_rm,
    p.nama AS nama_pasien,
    p.tanggal_lahir,
    p.jenis_kelamin,
    v.tanggal_kunjungan,
    v.no_antrian,
    d.nama AS nama_dokter,
    v.status
FROM patients p
LEFT JOIN visits v ON p.id = v.patient_id
LEFT JOIN doctors d ON v.doctor_id = d.id
WHERE p.id = 1
ORDER BY v.tanggal_kunjungan DESC
LIMIT 1;
```

### Get Doctor Schedule Today

```sql
SELECT 
    v.no_antrian,
    p.no_rm,
    p.nama AS nama_pasien,
    v.keluhan,
    v.status,
    v.tanggal_kunjungan
FROM visits v
INNER JOIN patients p ON v.patient_id = p.id
WHERE v.doctor_id = 1
  AND DATE(v.tanggal_kunjungan) = CURDATE()
ORDER BY v.no_antrian ASC;
```

### Get Patient Medical History

```sql
SELECT 
    mr.tanggal_periksa,
    d.nama AS nama_dokter,
    mr.diagnosa,
    mr.resep
FROM medical_records mr
INNER JOIN doctors d ON mr.doctor_id = d.id
WHERE mr.patient_id = 1
ORDER BY mr.tanggal_periksa DESC;
```

### Count Daily Statistics

```sql
SELECT 
    DATE(tanggal_kunjungan) AS tanggal,
    COUNT(*) AS total_kunjungan,
    SUM(CASE WHEN status = 'menunggu' THEN 1 ELSE 0 END) AS menunggu,
    SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) AS selesai
FROM visits
WHERE DATE(tanggal_kunjungan) = CURDATE()
GROUP BY DATE(tanggal_kunjungan);
```

---

## 📝 Database Maintenance

### Backup Command

```bash
mysqldump -u root -p simple_clinic > backup_$(date +%Y%m%d).sql
```

### Restore Command

```bash
mysql -u root -p simple_clinic < backup_20251208.sql
```

### Check Table Integrity

```sql
CHECK TABLE users, patients, doctors, visits, medical_records;
```

### Optimize Tables

```sql
OPTIMIZE TABLE users, patients, doctors, visits, medical_records;
```

---

## 🔐 Security Considerations

1. **Password Hashing**
   - ✅ ALWAYS use bcrypt (PASSWORD_BCRYPT)
   - ✅ Never store plain text passwords
   - ✅ Hash length: 255 characters (future-proof)

2. **Foreign Key Constraints**
   - ✅ Maintain referential integrity
   - ✅ Prevent orphan records
   - ✅ CASCADE DELETE for cleanup

3. **Data Validation**
   - ✅ NOT NULL for required fields
   - ✅ UNIQUE for RM, username
   - ✅ ENUM for fixed choices

4. **Sensitive Data**
   - ⚠️ Medical records are sensitive!
   - ⚠️ Implement access control in application layer
   - ⚠️ Log all medical record access

---

**Database Documentation Complete! 📚**

_Last Updated: December 8, 2025_
