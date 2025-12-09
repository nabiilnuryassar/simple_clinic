# 🛠️ Development Setup - Sistem Informasi Klinik Mutiara

Panduan lengkap untuk setup environment development lokal.

---

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Installation Steps](#installation-steps)
3. [Database Setup](#database-setup)
4. [Configuration](#configuration)
5. [Running the Application](#running-the-application)
6. [Development Tools](#development-tools)
7. [Troubleshooting](#troubleshooting)

---

## ✅ Prerequisites

### Required Software

| Software | Version | Download Link |
|----------|---------|---------------|
| **PHP** | 7.4+ (Recommended: 8.0+) | [php.net](https://www.php.net/downloads) |
| **MySQL** | 5.7+ / MariaDB 10.3+ | [mysql.com](https://dev.mysql.com/downloads/) |
| **Git** | Latest | [git-scm.com](https://git-scm.com/) |
| **Composer** | 2.0+ (Optional) | [getcomposer.org](https://getcomposer.org/) |

### PHP Extensions (Required)

```bash
# Check installed extensions
php -m

# Required extensions:
- PDO
- pdo_mysql
- mbstring
- session
- json
```

### Optional Tools

- **Code Editor:** VS Code, PhpStorm, Sublime Text
- **API Testing:** Postman, Insomnia
- **Database Client:** MySQL Workbench, phpMyAdmin, DBeaver

---

## 📦 Installation Steps

### 1. Clone Repository

```bash
# Clone project
git clone <repository-url>
cd simple-clinic

# Check structure
ls -la
```

**Expected output:**
```
assets/
config/
database/
documentation/
layout/
pages/
process/
index.php
.htaccess
README.md
```

---

### 2. Check PHP Installation

```bash
# Check PHP version
php -v
# Expected: PHP 7.4+ or higher

# Check PHP extensions
php -m | grep -E 'PDO|pdo_mysql|mbstring'
# Should show: PDO, pdo_mysql, mbstring

# Test PHP
php -r "echo 'PHP is working!';"
```

---

## 🗄️ Database Setup

### Option 1: Using MySQL Command Line

```bash
# 1. Login to MySQL
mysql -u root -p
# Enter your MySQL root password

# 2. Create database
CREATE DATABASE simple_clinic;

# 3. Create dedicated user (recommended)
CREATE USER 'clinic_dev'@'localhost' IDENTIFIED BY 'dev_password';

# 4. Grant privileges
GRANT ALL PRIVILEGES ON simple_clinic.* TO 'clinic_dev'@'localhost';
FLUSH PRIVILEGES;

# 5. Exit MySQL
EXIT;

# 6. Import schema
mysql -u clinic_dev -p simple_clinic < database/schema.sql
# Enter password: dev_password
```

### Option 2: Using phpMyAdmin

1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Click "New" to create database
3. Database name: `simple_clinic`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"
6. Select `simple_clinic` database
7. Click "Import" tab
8. Choose file: `database/schema.sql`
9. Click "Go"

### Verify Database

```bash
# Check tables created
mysql -u clinic_dev -p simple_clinic -e "SHOW TABLES;"

# Expected output:
# +-------------------------+
# | Tables_in_simple_clinic |
# +-------------------------+
# | doctors                 |
# | medical_records         |
# | patients                |
# | users                   |
# | visits                  |
# +-------------------------+

# Check seeded data
mysql -u clinic_dev -p simple_clinic -e "SELECT username, nama, role FROM users;"

# Expected output:
# +----------+-----------------+--------+
# | username | nama            | role   |
# +----------+-----------------+--------+
# | admin    | Administrator   | admin  |
# | dokter   | Dr. John Doe    | dokter |
# +----------+-----------------+--------+
```

---

## ⚙️ Configuration

### 1. Database Configuration

Edit `config/database.php`:

```php
<?php
// Development Database Settings
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'simple_clinic');
define('DB_USER', 'clinic_dev');        // User yang dibuat tadi
define('DB_PASS', 'dev_password');      // Password user
define('DB_CHARSET', 'utf8mb4');
```

### 2. Session Configuration

Edit `config/session.php` (optional untuk dev):

```php
// Development: Cookie secure bisa OFF (karena HTTP)
ini_set('session.cookie_secure', 0);    // 0 untuk HTTP, 1 untuk HTTPS

// Session lifetime (2 jam)
ini_set('session.gc_maxlifetime', 7200);
```

### 3. Test Configuration

```bash
# Test database connection
php -r "require 'config/database.php'; \$db = get_db_connection(); echo 'Database connected successfully!';"
```

**Expected output:**
```
Database connected successfully!
```

---

## 🚀 Running the Application

### Option 1: PHP Built-in Server (Recommended for Development)

```bash
# Start server
php -S localhost:8000

# Or with custom host/port
php -S 0.0.0.0:8080

# Run in background
php -S localhost:8000 > /dev/null 2>&1 &
```

**Access application:**
- URL: http://localhost:8000
- Login page: http://localhost:8000/pages/auth/login.php

### Option 2: XAMPP

1. Copy project ke `C:\xampp\htdocs\simple-clinic\` (Windows)
2. Start XAMPP Control Panel
3. Start Apache dan MySQL
4. Access: http://localhost/simple-clinic/

### Option 3: Laragon (Windows)

1. Copy project ke `C:\laragon\www\simple-clinic\`
2. Start Laragon
3. Laragon auto-detect project
4. Access: http://simple-clinic.test/

---

## 🔐 Default Login Credentials

| Role | Username | Password | Access |
|------|----------|----------|--------|
| **Admin** | `admin` | `admin123` | Full system access |
| **Dokter** | `dokter` | `dokter123` | Patient examination |

> 💡 **Tip:** Credentials ini hanya untuk development. Ganti sebelum deploy ke production!

---

## 🧰 Development Tools

### 1. Generate Password Hash

```bash
# Generate bcrypt hash untuk password baru
php generate_password.php

# Input: mypassword
# Output: $2y$10$abcd1234...
```

### 2. Database Seeding

Tambah data dummy untuk testing:

```sql
-- Login to MySQL
mysql -u clinic_dev -p simple_clinic

-- Add dummy patients
INSERT INTO patients (no_rm, nama, tanggal_lahir, jenis_kelamin, alamat, no_telp) 
VALUES 
('RM-2025-0004', 'Test Patient 1', '1990-01-01', 'L', 'Jl. Test No. 1', '08123456789'),
('RM-2025-0005', 'Test Patient 2', '1995-05-15', 'P', 'Jl. Test No. 2', '08198765432');
```

### 3. Clear Sessions (if stuck)

```bash
# Linux/Mac
rm /tmp/sess_*

# Windows (XAMPP)
# Delete files di: C:\xampp\tmp\
```

### 4. View Logs

```bash
# PHP error log
tail -f /var/log/php/error.log

# Apache error log (XAMPP)
tail -f C:\xampp\apache\logs\error.log

# Custom application log (if you add logging)
tail -f logs/app.log
```

---

## 🧪 Testing

### Quick Health Check

```bash
# 1. Test homepage
curl http://localhost:8000/

# 2. Test login page
curl http://localhost:8000/pages/auth/login.php

# 3. Test CSS loading
curl -I http://localhost:8000/assets/css/style.css

# Expected: HTTP/1.1 200 OK
```

### Manual Testing Checklist

- [ ] Login sebagai admin
- [ ] Tambah pasien baru
- [ ] Tambah dokter baru
- [ ] Buat antrian kunjungan
- [ ] Login sebagai dokter
- [ ] Periksa pasien
- [ ] Isi rekam medis
- [ ] Logout

---

## 🐛 Troubleshooting

### Problem 1: Database Connection Failed

**Error:**
```
SQLSTATE[HY000] [1045] Access denied for user 'clinic_dev'@'localhost'
```

**Solution:**
```bash
# Check user exists
mysql -u root -p -e "SELECT User, Host FROM mysql.user WHERE User='clinic_dev';"

# Recreate user if needed
mysql -u root -p -e "DROP USER 'clinic_dev'@'localhost';"
mysql -u root -p -e "CREATE USER 'clinic_dev'@'localhost' IDENTIFIED BY 'dev_password';"
mysql -u root -p -e "GRANT ALL PRIVILEGES ON simple_clinic.* TO 'clinic_dev'@'localhost';"
```

---

### Problem 2: CSS Not Loading

**Error:** 404 for `/assets/css/style.css`

**Solution:**
```bash
# Check file exists
ls -la assets/css/style.css

# Check permissions
chmod 644 assets/css/style.css

# Test direct access
curl -I http://localhost:8000/assets/css/style.css
```

---

### Problem 3: Session Not Working

**Error:** Login berhasil tapi redirect ke login lagi

**Solution:**
```bash
# Check session directory writable
# Linux
ls -ld /tmp
chmod 1777 /tmp

# Check PHP session settings
php -i | grep session.save_path
```

---

### Problem 4: Port Already in Use

**Error:**
```
Failed to listen on localhost:8000 (reason: Address already in use)
```

**Solution:**
```bash
# Find process using port 8000
lsof -i :8000
# or
netstat -tuln | grep 8000

# Kill the process
kill -9 <PID>

# Use different port
php -S localhost:8001
```

---

## 📁 Project Structure (Development Files)

```
simple-clinic/
├── config/                     # Configuration files
│   ├── database.php           # 🔧 Edit DB credentials here
│   ├── session.php            # Session settings
│   ├── helper.php             # Utility functions
│   └── url_helper.php         # URL & CSRF helpers
│
├── database/
│   └── schema.sql             # Database structure + seeding
│
├── generate_password.php      # Password hash generator
│
└── documentation/
    ├── DEV_SETUP.md          # 📖 This file
    ├── STAGING_DEPLOY.md     # Staging deployment guide
    └── PRODUCTION_DEPLOY.md  # Production deployment guide
```

---

## 🎯 Development Workflow

### Daily Development Routine

```bash
# 1. Pull latest changes
git pull origin main

# 2. Start server
php -S localhost:8000

# 3. Open browser
# http://localhost:8000

# 4. Make changes to code

# 5. Test changes
# Refresh browser, check functionality

# 6. Commit changes
git add .
git commit -m "Feature: Add patient search"
git push origin main
```

---

## 📝 Code Style Guide

### PHP Conventions

```php
// ✅ Good: snake_case for variables
$nama_pasien = "John Doe";
$total_kunjungan = 10;

// ❌ Bad: camelCase
$namaPasien = "John Doe";

// ✅ Good: Descriptive function names
function get_patient_by_id($id) { }

// ❌ Bad: Abbreviations
function getPtById($id) { }
```

### File Naming

```
✅ auth_login.php
✅ admin_add_pasien.php
❌ AuthLogin.php
❌ adminAddPasien.php
```

---

## 🔄 Next Steps

After successful setup:

1. ✅ Read **USER_GUIDE.md** untuk memahami fitur aplikasi
2. ✅ Read **TESTING.md** untuk testing procedures
3. ✅ Explore codebase dan modifikasi sesuai kebutuhan
4. ✅ When ready for staging: Read **STAGING_DEPLOY.md**

---

## 💡 Development Tips

### Hot Reload (Manual)

PHP built-in server tidak auto-reload. Refresh browser setelah edit code.

### Debug Mode

Add di top of `index.php` untuk development:

```php
<?php
// Development only!
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
```

**⚠️ REMOVE sebelum deploy ke production!**

### Database Backup Before Testing

```bash
# Backup before major changes
mysqldump -u clinic_dev -p simple_clinic > backup_before_test.sql

# Restore if something breaks
mysql -u clinic_dev -p simple_clinic < backup_before_test.sql
```

---

## 📞 Need Help?

- **Documentation:** Check other `.md` files in `/documentation/`
- **Code Comments:** Setiap file ada komentar penjelasan
- **Database Schema:** Lihat `database/schema.sql`

---

**Happy Coding! 🚀**

_Last Updated: December 8, 2025_
