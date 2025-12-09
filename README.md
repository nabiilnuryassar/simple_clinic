# 🏥 Sistem Informasi Klinik Mutiara (Simple PHP Native)

[![PHP Version](https://img.shields.io/badge/PHP-8.3%2B-blue)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

Sistem Informasi Manajemen Klinik berbasis web yang dibangun dengan **PHP Native** (tanpa framework) untuk pembelajaran rekayasa perangkat lunak. Aplikasi ini mengelola data pasien, dokter, antrian kunjungan, dan rekam medis dengan standar keamanan web modern.

## ✨ Features

### 🔐 Authentication & Authorization
- Login system dengan password hashing (bcrypt)
- Role-based access control (Admin & Dokter)
- CSRF protection pada semua form
- Session management yang aman

### 👨‍💼 Admin Module
- **Dashboard** - Statistik real-time (total pasien, dokter, kunjungan)
- **Manajemen Pasien** - CRUD pasien dengan auto-generate nomor rekam medis
- **Manajemen Dokter** - CRUD dokter beserta akun login
- **Antrian Kunjungan** - Buat dan kelola antrian pasien harian

### 👨‍⚕️ Doctor Module
- **Dashboard Dokter** - Statistik pasien personal (hari ini, menunggu, selesai)
- **Pemeriksaan Pasien** - Form lengkap untuk input rekam medis
- **Rekam Medis** - Anamnesa, diagnosa, tindakan, dan resep obat

### 🛡️ Security Features
- PDO prepared statements (SQL injection protection)
- XSS protection dengan input sanitization
- Password hashing menggunakan bcrypt
- CSRF token validation
- Session security (httponly, samesite)
- Secure headers configuration

---

## 📋 System Requirements

- **PHP:** 7.4+ (Recommended: 8.0+)
- **Database:** MySQL 5.7+ / MariaDB 10.3+
- **Web Server:** Apache / PHP Built-in Server
- **Browser:** Chrome, Firefox, Edge (latest version)

---

## 🚀 Quick Start

**Untuk mahasiswa:** Fokus pada development lokal

→ **[documentation/DEV_SETUP.md](documentation/DEV_SETUP.md)** - Setup lengkap PHP, MySQL & jalankan aplikasi

---

## ⚡ Super Quick Setup (Development)

```bash
# 1. Clone repository
git clone <repository-url>
cd simple-clinic

# 2. Create database
mysql -u root -p -e "CREATE DATABASE simple_clinic;"
mysql -u root -p simple_clinic < database/schema.sql

# 3. Configure database (edit config/database.php)
# Set your MySQL credentials

# 4. Start server
php -S localhost:8000

# 5. Open browser: http://localhost:8000
# Login: admin/admin123 (Admin) or dokter/dokter123 (Dokter)
```

**⚠️ Ganti password default sebelum production!**

📖 **Untuk panduan lengkap step-by-step:** [documentation/DEV_SETUP.md](documentation/DEV_SETUP.md)

---

## 📂 Project Structure

```
simple-clinic/
├── config/                  # Konfigurasi & Utilities
│   ├── database.php         # PDO database connection
│   ├── session.php          # Session management & security
│   ├── helper.php           # Helper functions (sanitize, hash, etc)
│   └── url_helper.php       # URL & routing helpers
│
├── assets/                  # Static files
│   ├── css/style.css        # Pure CSS (no framework)
│   └── img/                 # Images & logo
│
├── layout/                  # Reusable UI components
│   ├── header.php           # HTML head & navbar
│   ├── sidebar.php          # Dynamic sidebar menu
│   └── footer.php           # Footer
│
├── pages/                   # Application pages (Views)
│   ├── auth/
│   │   └── login.php        # Login page
│   ├── admin/               # Admin pages
│   │   ├── dashboard.php
│   │   ├── pasien.php       # Patient CRUD
│   │   ├── dokter.php       # Doctor CRUD
│   │   └── antrian.php      # Queue management
│   └── doctor/              # Doctor pages
│       ├── dashboard.php
│       └── periksa.php      # Patient examination
│
├── process/                 # Backend logic (Controllers)
│   ├── auth_login.php       # Login handler
│   ├── auth_logout.php      # Logout handler
│   ├── admin_add_pasien.php # Add patient
│   ├── admin_add_dokter.php # Add doctor
│   ├── admin_add_visit.php  # Add visit queue
│   └── doctor_update_rm.php # Save medical record
│
├── database/
│   └── schema.sql           # Database schema & seeding
│
├── index.php                # Application entry point
├── .htaccess                # Apache security config
└── README.md                # This file
```

---

## 📚 Dokumentasi

Dokumentasi lengkap tersedia di folder `documentation/`:

### 🚀 Mulai Disini
- **[DOCUMENTATION.md](documentation/DOCUMENTATION.md)** - Panduan navigasi semua dokumentasi

### 🛠️ Setup & Development
- **[DEV_SETUP.md](documentation/DEV_SETUP.md)** - Install PHP, MySQL, jalankan aplikasi

### 🏗️ Technical Docs
- **[DATABASE_SCHEMA.md](documentation/DATABASE_SCHEMA.md)** - Struktur database & relasi
- **[CODEBASE_GUIDE.md](documentation/CODEBASE_GUIDE.md)** - Arsitektur code & fungsi file

### 📖 Usage & Testing
- **[USER_GUIDE.md](documentation/USER_GUIDE.md)** - Cara pakai fitur Admin & Dokter
- **[TESTING.md](documentation/TESTING.md)** - Test cases & cara testing

### ⚡ Utilities
- **[QUICK_REFERENCE.md](documentation/QUICK_REFERENCE.md)** - Command shortcuts

---

## 📖 Quick Info

### Default Login Credentials
- **Admin:** username `admin` / password `admin123`
- **Dokter:** username `dokter` / password `dokter123`

⚠️ **Ganti password default sebelum production!**

### Fitur Utama
- Manajemen pasien dengan auto-generate nomor RM
- Manajemen dokter & akun
- Antrian kunjungan harian
- Rekam medis lengkap (anamnesa, diagnosa, resep)
- Role-based access (Admin & Dokter)

---

## 📖 User Guide

### Untuk Admin

1. **Login** menggunakan akun admin
2. **Dashboard** - Lihat statistik keseluruhan sistem
3. **Kelola Pasien:**
   - Tambah pasien baru (otomatis generate No. RM)
   - View/Edit/Delete data pasien
4. **Kelola Dokter:**
   - Tambah dokter baru beserta akun login
   - Kelola status dokter (aktif/nonaktif)
5. **Buat Antrian:**
   - Pilih pasien dan dokter
   - Input keluhan utama
   - Sistem auto-generate nomor antrian

### Untuk Dokter

1. **Login** menggunakan akun dokter
2. **Dashboard** - Lihat statistik pasien Anda hari ini
3. **Periksa Pasien:**
   - Lihat daftar pasien yang menunggu
   - Pilih pasien untuk diperiksa
   - Isi form pemeriksaan:
     - Anamnesa (riwayat penyakit)
     - Diagnosa
     - Tindakan medis
     - Resep obat
     - Catatan tambahan
   - Submit untuk menyelesaikan pemeriksaan

---

## 🔒 Security Best Practices

Aplikasi ini menerapkan standar keamanan web modern:

### Input Validation & Sanitization
```php
// Semua input user di-sanitize
$clean_input = clean_input($_POST['data']);

// Output di-escape untuk mencegah XSS
echo escape_html($user_data);
```

### SQL Injection Protection
```php
// Menggunakan PDO prepared statements
$stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
```

### Password Security
```php
// Hash password dengan bcrypt
$hashed = password_hash($password, PASSWORD_BCRYPT);

// Verify password
password_verify($input_password, $stored_hash);
```

### CSRF Protection
```php
// Generate token di form
<?php echo csrf_field(); ?>

// Validasi di backend
verify_csrf_token($_POST['csrf_token']);
```

---

## 📖 Documentation

Dokumentasi lengkap dipisahkan berdasarkan environment untuk kemudahan navigasi:

### 📋 General
- **[DOCUMENTATION.md](documentation/DOCUMENTATION.md)** - 📍 Indeks & navigasi semua dokumentasi
- **[USER_GUIDE.md](documentation/USER_GUIDE.md)** - 👥 Panduan lengkap Admin & Dokter
- **[TESTING.md](documentation/TESTING.md)** - 🧪 Test cases & validation
- **[PROJECT_STATUS.md](documentation/PROJECT_STATUS.md)** - 📊 Progress & roadmap
- **[QUICK_REFERENCE.md](documentation/QUICK_REFERENCE.md)** - 🎯 Command cheat sheet

### 🛠️ Development Environment
- **[DEV_SETUP.md](documentation/DEV_SETUP.md)** - Setup lokal (PHP, MySQL, XAMPP)

### 🧪 Staging Environment
- **[STAGING_DEPLOY.md](documentation/STAGING_DEPLOY.md)** - Deploy ke VPS staging untuk QA

### 🚀 Production Environment  
- **[PRODUCTION_DEPLOY.md](documentation/PRODUCTION_DEPLOY.md)** - Deploy production dengan SSL & security

> 💡 **Tip:** Mulai dengan [DOCUMENTATION.md](documentation/DOCUMENTATION.md) untuk navigasi lengkap berdasarkan role Anda.

---

## 📝 Coding Conventions

### Variable Naming
- Gunakan `snake_case`: `$nama_pasien`, `$total_kunjungan`
- Hindari `camelCase`: ~~`$namaPasien`~~

### File Naming
- Lowercase dengan underscore: `auth_login.php`, `admin_add_pasien.php`
- Hindari PascalCase: ~~`AuthLogin.php`~~

### Function Naming
- Descriptive dan action-oriented: `get_db_connection()`, `verify_csrf_token()`
- Return boolean untuk check functions: `is_logged_in()`, `has_role()`

### Comments
```php
// Single line untuk penjelasan singkat
// Proses validasi input user

/**
 * Multi-line untuk fungsi kompleks
 * @param string $username Username user
 * @return array|null User data atau null
 */
function get_user_by_username($username) { }
```

---

## 🚧 Development Roadmap

### Phase 1: ✅ Completed
- [x] Database design & ERD
- [x] Folder structure setup
- [x] Authentication system
- [x] Admin & Doctor modules
- [x] Security implementation

### Phase 2: 🔄 In Progress
- [ ] Advanced search & filtering
- [ ] Pagination for large datasets
- [ ] Print medical record (PDF)
- [ ] Data export (Excel/CSV)

### Phase 3: 📋 Planned
- [ ] Appointment scheduling
- [ ] Medicine inventory
- [ ] Payment & billing system
- [ ] Reporting & analytics

---

## 🐛 Known Issues

1. **CSS Loading** - Minor path issue di beberapa subdirectory (Fixed ✅)
2. **Form Validation** - Client-side validation bisa ditambahkan untuk UX lebih baik
3. **Mobile Responsive** - UI optimized untuk desktop, mobile masih basic

---

## 📄 License

Project ini dibuat untuk tujuan pembelajaran. Silakan gunakan dan modifikasi sesuai kebutuhan.

**MIT License** - Lihat file `LICENSE` untuk detail.

---

## 👥 Contributors

- **Developer:** Nabiil
- **Purpose:** Skripsi/Tugas Akhir - Sistem Informasi Klinik
- **Tech Stack:** PHP Native, MySQL, Pure CSS

---

## 📞 Support

Jika ada pertanyaan atau issue:

1. Baca dokumentasi lengkap: `INSTALLATION.md`, `TESTING.md`
2. Check database schema: `database/schema.sql`
3. Review code comments untuk detail implementasi

---

## 🎓 Learning Resources

Project ini cocok untuk belajar:
- PHP Native (tanpa framework)
- Database design & normalization
- Web security best practices
- Session management
- CRUD operations
- Authentication & authorization

**Recommended Next Steps:**
- Pelajari PHP frameworks (Laravel, CodeIgniter)
- Eksplorasi JavaScript untuk interactivity
- Implementasi REST API
- Deploy ke production server

---

**Made with ❤️ for learning purposes**

_Last Updated: December 8, 2025_
