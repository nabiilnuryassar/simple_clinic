# 🧪 Staging Deployment Guide - Sistem Informasi Klinik Mutiara

Panduan deployment ke staging environment untuk testing sebelum production.

---

## 📋 Table of Contents

1. [What is Staging?](#what-is-staging)
2. [Server Requirements](#server-requirements)
3. [Pre-Deployment Checklist](#pre-deployment-checklist)
4. [Deployment Steps](#deployment-steps)
5. [Configuration](#configuration)
6. [Testing in Staging](#testing-in-staging)
7. [Troubleshooting](#troubleshooting)

---

## 🎯 What is Staging?

**Staging** adalah environment yang identik dengan production untuk testing sebelum go-live.

### Purpose:
- ✅ Test aplikasi di environment production-like
- ✅ Verify deployment process
- ✅ QA testing tanpa risk production downtime
- ✅ Demo ke stakeholder/client
- ✅ Load testing dan performance tuning

### Staging vs Development vs Production

| Aspect | Development | Staging | Production |
|--------|-------------|---------|------------|
| **Purpose** | Coding & debugging | Testing & QA | Live system |
| **Database** | Small test data | Copy of production | Real data |
| **Errors** | Display on screen | Log only | Log only |
| **Security** | Relaxed | Production-like | Strict |
| **Access** | Developer only | Team + QA | Public/Users |
| **Uptime** | Not critical | Important | Critical |

---

## 🖥️ Server Requirements

### Minimum Specs

- **OS:** Ubuntu 20.04 LTS / CentOS 7+ / Debian 10+
- **CPU:** 2 vCPU
- **RAM:** 2 GB
- **Storage:** 20 GB SSD
- **PHP:** 7.4+ (Recommended: 8.0+)
- **MySQL:** 5.7+ / MariaDB 10.3+
- **Web Server:** Apache 2.4+ / Nginx 1.18+

### Recommended Providers

- **VPS:** DigitalOcean, Linode, Vultr ($5-10/month)
- **Cloud:** AWS EC2 t2.micro, Google Cloud e2-micro
- **Shared Hosting:** Hostinger, Niagahoster (untuk testing kecil)

---

## ✅ Pre-Deployment Checklist

### 1. Server Access

- [ ] SSH access ke staging server
- [ ] Root/sudo privileges
- [ ] Domain/subdomain ready (e.g., `staging.clinic.com`)

### 2. Code Preparation

```bash
# Di local development
git checkout main
git pull origin main

# Pastikan semua changes committed
git status

# Tag version untuk staging
git tag -a v1.0.0-staging -m "Staging release v1.0.0"
git push origin v1.0.0-staging
```

### 3. Database Backup

```bash
# Backup production database (if exists)
mysqldump -u root -p production_clinic > backup_before_staging.sql

# Or export dari phpMyAdmin
```

---

## 🚀 Deployment Steps

### Step 1: Server Setup

#### Install LAMP Stack

```bash
# SSH ke staging server
ssh user@staging-server-ip

# Update system
sudo apt update && sudo apt upgrade -y

# Install Apache
sudo apt install apache2 -y

# Install PHP 8.0
sudo apt install php8.0 php8.0-cli php8.0-fpm php8.0-mysql php8.0-mbstring php8.0-xml php8.0-curl -y

# Install MySQL
sudo apt install mysql-server -y

# Secure MySQL
sudo mysql_secure_installation
```

---

### Step 2: Configure MySQL

```bash
# Login to MySQL
sudo mysql -u root -p

# Create staging database
CREATE DATABASE simple_clinic_staging CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Create staging user
CREATE USER 'clinic_staging'@'localhost' IDENTIFIED BY 'staging_secure_password_here';

# Grant privileges
GRANT SELECT, INSERT, UPDATE, DELETE ON simple_clinic_staging.* TO 'clinic_staging'@'localhost';

FLUSH PRIVILEGES;
EXIT;
```

---

### Step 3: Deploy Code

#### Option A: Git Deployment (Recommended)

```bash
# Navigate to web root
cd /var/www/

# Clone repository
sudo git clone <repository-url> simple-clinic-staging

# Checkout staging tag
cd simple-clinic-staging
sudo git checkout v1.0.0-staging

# Set permissions
sudo chown -R www-data:www-data /var/www/simple-clinic-staging
sudo chmod -R 755 /var/www/simple-clinic-staging
```

#### Option B: FTP/SCP Upload

```bash
# From local machine, zip project
tar -czf simple-clinic.tar.gz simple-clinic/

# Upload via SCP
scp simple-clinic.tar.gz user@staging-server:/tmp/

# On server, extract
cd /var/www/
sudo tar -xzf /tmp/simple-clinic.tar.gz
sudo mv simple-clinic simple-clinic-staging
```

---

### Step 4: Import Database

```bash
# On staging server
mysql -u clinic_staging -p simple_clinic_staging < /var/www/simple-clinic-staging/database/schema.sql

# Verify tables imported
mysql -u clinic_staging -p simple_clinic_staging -e "SHOW TABLES;"
```

---

### Step 5: Configure Apache

```bash
# Create virtual host
sudo nano /etc/apache2/sites-available/clinic-staging.conf
```

**Content:**
```apache
<VirtualHost *:80>
    ServerName staging.clinic.com
    ServerAlias www.staging.clinic.com
    DocumentRoot /var/www/simple-clinic-staging
    
    <Directory /var/www/simple-clinic-staging>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/clinic-staging-error.log
    CustomLog ${APACHE_LOG_DIR}/clinic-staging-access.log combined
</VirtualHost>
```

**Enable site:**
```bash
# Enable site
sudo a2ensite clinic-staging.conf

# Enable mod_rewrite
sudo a2enmod rewrite

# Test configuration
sudo apache2ctl configtest

# Restart Apache
sudo systemctl restart apache2
```

---

## ⚙️ Configuration

### 1. Database Configuration

Edit `/var/www/simple-clinic-staging/config/database.php`:

```php
<?php
// Staging Database Configuration
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'simple_clinic_staging');
define('DB_USER', 'clinic_staging');
define('DB_PASS', 'staging_secure_password_here');  // Password yang dibuat di Step 2
define('DB_CHARSET', 'utf8mb4');
```

---

### 2. Session Security

Edit `/var/www/simple-clinic-staging/config/session.php`:

```php
// Staging: Enable secure cookies (if using HTTPS)
ini_set('session.cookie_secure', 1);      // 1 jika sudah setup SSL
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

// Session lifetime (2 jam)
ini_set('session.gc_maxlifetime', 7200);
```

---

### 3. Error Handling

Edit `/var/www/simple-clinic-staging/index.php`:

```php
<?php
// Staging: Log errors, don't display
error_reporting(E_ALL);
ini_set('display_errors', 0);           // Don't show errors on screen
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/php/staging_error.log');

// Rest of code...
```

---

### 4. Create Error Log Directory

```bash
sudo mkdir -p /var/log/php
sudo chown www-data:www-data /var/log/php
sudo chmod 755 /var/log/php
```

---

### 5. File Permissions

```bash
cd /var/www/simple-clinic-staging

# Directories: 755
sudo find . -type d -exec chmod 755 {} \;

# Files: 644
sudo find . -type f -exec chmod 644 {} \;

# Config files: 640 (more secure)
sudo chmod 640 config/database.php

# Set owner
sudo chown -R www-data:www-data .
```

---

## 🧪 Testing in Staging

### 1. Basic Health Check

```bash
# Test from server
curl -I http://staging.clinic.com

# Expected: HTTP/1.1 200 OK
```

### 2. Access from Browser

Open: `http://staging.clinic.com`

**Expected:**
- Login page muncul
- CSS loading correctly
- No errors di console browser (F12)

---

### 3. Functional Testing

**Admin Testing:**
- [ ] Login sebagai admin (`admin` / `admin123`)
- [ ] View dashboard - lihat statistik
- [ ] Tambah pasien baru
- [ ] Edit pasien existing
- [ ] Tambah dokter baru
- [ ] Buat antrian kunjungan
- [ ] Logout

**Dokter Testing:**
- [ ] Login sebagai dokter (`dokter` / `dokter123`)
- [ ] View dashboard dokter
- [ ] Lihat daftar pasien menunggu
- [ ] Periksa pasien - isi rekam medis
- [ ] Submit pemeriksaan
- [ ] Verify rekam medis tersimpan
- [ ] Logout

---

### 4. Security Testing

```bash
# Test SQL injection (should be prevented)
curl -X POST http://staging.clinic.com/process/auth_login.php \
  -d "username=admin' OR '1'='1&password=test"

# Expected: Login fails, no SQL error

# Test CSRF protection
curl -X POST http://staging.clinic.com/process/admin_add_pasien.php \
  -d "nama=Test&alamat=Test"

# Expected: CSRF validation error
```

---

### 5. Performance Testing

```bash
# Install Apache Bench
sudo apt install apache2-utils -y

# Test 100 requests, 10 concurrent
ab -n 100 -c 10 http://staging.clinic.com/

# Check response time, requests per second
```

---

## 🔐 Security Hardening

### 1. Disable Directory Listing

Already set in `.htaccess`:
```apache
Options -Indexes
```

### 2. Hide PHP Version

Edit `/etc/php/8.0/apache2/php.ini`:
```ini
expose_php = Off
```

Restart Apache:
```bash
sudo systemctl restart apache2
```

---

### 3. Setup SSL Certificate (Optional but Recommended)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache -y

# Get certificate
sudo certbot --apache -d staging.clinic.com

# Auto-renewal test
sudo certbot renew --dry-run
```

After SSL:
- Update `session.cookie_secure` to `1`
- All traffic redirected to HTTPS

---

## 🐛 Troubleshooting

### Problem 1: 500 Internal Server Error

**Check Apache error log:**
```bash
sudo tail -f /var/log/apache2/clinic-staging-error.log
```

**Common causes:**
- Wrong file permissions
- PHP syntax error
- Missing PHP extension
- Database connection failed

---

### Problem 2: Database Connection Failed

**Test connection:**
```bash
php -r "
\$db = new PDO('mysql:host=localhost;dbname=simple_clinic_staging', 'clinic_staging', 'password');
echo 'Connected!';
"
```

**Check user privileges:**
```bash
mysql -u root -p -e "SHOW GRANTS FOR 'clinic_staging'@'localhost';"
```

---

### Problem 3: CSS Not Loading

**Check Apache config:**
```bash
# Enable AllowOverride All
sudo nano /etc/apache2/sites-available/clinic-staging.conf
```

**Test .htaccess:**
```bash
# Check if mod_rewrite enabled
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

### Problem 4: Session Not Persisting

**Check session directory:**
```bash
# Check PHP session path
php -i | grep session.save_path

# Check writable
ls -ld /var/lib/php/sessions
sudo chmod 1733 /var/lib/php/sessions
```

---

## 📊 Monitoring

### 1. View Logs

```bash
# Apache access log
sudo tail -f /var/log/apache2/clinic-staging-access.log

# Apache error log
sudo tail -f /var/log/apache2/clinic-staging-error.log

# PHP error log
sudo tail -f /var/log/php/staging_error.log

# MySQL error log
sudo tail -f /var/log/mysql/error.log
```

---

### 2. Database Monitoring

```bash
# Check active connections
mysql -u root -p -e "SHOW PROCESSLIST;"

# Check table sizes
mysql -u root -p simple_clinic_staging -e "
SELECT table_name, 
       ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)' 
FROM information_schema.TABLES 
WHERE table_schema = 'simple_clinic_staging';"
```

---

## 🔄 Update Process

### Deploy New Version

```bash
# SSH to staging server
ssh user@staging-server

# Navigate to project
cd /var/www/simple-clinic-staging

# Backup current version
sudo cp -r . ../simple-clinic-backup-$(date +%Y%m%d)

# Pull latest changes
sudo git fetch --all
sudo git checkout v1.1.0-staging

# Update database if needed
mysql -u clinic_staging -p simple_clinic_staging < database/migrations/v1.1.0.sql

# Clear cache/sessions
sudo rm -rf /tmp/sess_*

# Restart Apache
sudo systemctl restart apache2

# Test
curl -I http://staging.clinic.com
```

---

## 📋 Staging Checklist

Before marking staging as ready:

### Functionality
- [ ] All CRUD operations working
- [ ] Authentication & authorization working
- [ ] Form validation working
- [ ] CSRF protection active
- [ ] Session management working

### Performance
- [ ] Page load < 2 seconds
- [ ] Database queries optimized
- [ ] No N+1 query issues

### Security
- [ ] SQL injection protected
- [ ] XSS protected
- [ ] CSRF protected
- [ ] Passwords hashed (bcrypt)
- [ ] Session secure cookies enabled

### Infrastructure
- [ ] Backup strategy in place
- [ ] Error logging configured
- [ ] Monitoring setup
- [ ] SSL certificate (optional)

---

## ➡️ Next Steps

Setelah staging verified dan approved:

1. ✅ Document any issues found
2. ✅ Fix bugs di development
3. ✅ Re-deploy to staging untuk re-test
4. ✅ Get stakeholder approval
5. ✅ **Ready for production!** → Read `PRODUCTION_DEPLOY.md`

---

**Staging Environment Ready! 🎉**

_Last Updated: December 8, 2025_
