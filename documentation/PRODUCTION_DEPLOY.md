# 🚀 Production Deployment Guide - Sistem Informasi Klinik X

Panduan lengkap deployment ke production environment dengan security best practices.

---

## ⚠️ CRITICAL: Read Before Deployment

**Production deployment adalah proses yang TIDAK BOLEH GAGAL.** Ikuti setiap step dengan teliti.

### Pre-Deployment Requirements

- ✅ Application telah ditest di staging
- ✅ All bugs resolved
- ✅ Stakeholder approval received
- ✅ Backup strategy prepared
- ✅ Rollback plan documented
- ✅ Maintenance window scheduled

---

## 📋 Table of Contents

1. [Server Requirements](#server-requirements)
2. [Pre-Deployment Checklist](#pre-deployment-checklist)
3. [Deployment Steps](#deployment-steps)
4. [Security Hardening](#security-hardening)
5. [Backup & Recovery](#backup--recovery)
6. [Monitoring & Maintenance](#monitoring--maintenance)
7. [Troubleshooting](#troubleshooting)

---

## 🖥️ Server Requirements

### Minimum Production Specs

- **OS:** Ubuntu 20.04 LTS / CentOS 8+ / Debian 11+
- **CPU:** 4 vCPU (untuk concurrent users)
- **RAM:** 4 GB (minimum), 8 GB (recommended)
- **Storage:** 50 GB SSD (dengan space untuk backup & logs)
- **Network:** Static IP, dedicated domain
- **PHP:** 8.0+ (latest stable)
- **MySQL:** 8.0+ / MariaDB 10.5+
- **Web Server:** Nginx (recommended) or Apache 2.4+

### Recommended Infrastructure

**Small Clinic (< 50 users):**
- VPS: DigitalOcean Droplet $20/month (4GB RAM, 2 vCPU)
- Database: Same server
- Backup: Daily automated to external storage

**Medium Clinic (50-200 users):**
- Web Server: 2x VPS (load balanced)
- Database: Separate server (managed MySQL)
- Backup: Hourly to S3/similar
- CDN: Cloudflare for static assets

**Large Clinic (200+ users):**
- Kubernetes cluster / AWS ECS
- RDS managed database
- Redis caching
- Auto-scaling

---

## ✅ Pre-Deployment Checklist

### 1. Code Preparation

```bash
# Di local development
git checkout production
git merge staging

# Tag production release
git tag -a v1.0.0 -m "Production release v1.0.0"
git push origin v1.0.0

# Create release notes
git log v0.9.0..v1.0.0 --oneline > RELEASE_NOTES.md
```

---

### 2. Security Audit

- [ ] All passwords changed from defaults
- [ ] No hardcoded credentials in code
- [ ] All secrets in environment variables
- [ ] SQL injection tests passed
- [ ] XSS protection verified
- [ ] CSRF tokens working
- [ ] SSL certificate ready
- [ ] Firewall rules configured

---

### 3. Database Planning

- [ ] Production database created
- [ ] Database user with minimal privileges
- [ ] Backup of staging database
- [ ] Migration scripts tested
- [ ] Database indexes optimized
- [ ] Foreign keys verified

---

### 4. Documentation

- [ ] README.md updated
- [ ] API documentation (if any)
- [ ] Admin user guide
- [ ] Incident response plan
- [ ] Contact list (developers, sysadmin, stakeholders)

---

## 🚀 Deployment Steps

### Step 1: Server Setup

```bash
# SSH ke production server
ssh root@production-server-ip

# Update system
apt update && apt upgrade -y

# Install firewall
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
ufw enable
```

---

### Step 2: Install LEMP Stack (Nginx + PHP + MySQL)

#### Install Nginx

```bash
apt install nginx -y
systemctl start nginx
systemctl enable nginx
```

#### Install PHP 8.0

```bash
apt install php8.0-fpm php8.0-mysql php8.0-mbstring php8.0-xml php8.0-curl php8.0-zip php8.0-gd php8.0-intl -y

# Configure PHP-FPM
nano /etc/php/8.0/fpm/php.ini
```

**Production php.ini settings:**
```ini
; Security
expose_php = Off
allow_url_fopen = Off
allow_url_include = Off
disable_functions = exec,passthru,shell_exec,system,proc_open,popen

; Error handling (PRODUCTION)
display_errors = Off
display_startup_errors = Off
log_errors = On
error_log = /var/log/php/error.log
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT

; Performance
memory_limit = 256M
max_execution_time = 60
max_input_time = 60
post_max_size = 20M
upload_max_filesize = 20M

; Session security
session.cookie_httponly = 1
session.cookie_secure = 1
session.use_strict_mode = 1
session.cookie_samesite = Strict
session.gc_maxlifetime = 7200

; OPcache
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 2
opcache.fast_shutdown = 1
```

**Restart PHP-FPM:**
```bash
systemctl restart php8.0-fpm
systemctl enable php8.0-fpm
```

---

#### Install MySQL 8.0

```bash
apt install mysql-server -y

# Secure installation
mysql_secure_installation
# Answer YES to all questions
# Set strong root password (min 16 characters)
```

---

### Step 3: Configure MySQL

```bash
mysql -u root -p
```

```sql
-- Create production database
CREATE DATABASE simple_clinic CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create production user dengan STRONG password
CREATE USER 'clinic_prod'@'localhost' IDENTIFIED BY 'CHANGE_THIS_VERY_STRONG_PASSWORD_2025!@#$%';

-- Grant MINIMAL privileges (no DROP, ALTER)
GRANT SELECT, INSERT, UPDATE, DELETE ON simple_clinic.* TO 'clinic_prod'@'localhost';

FLUSH PRIVILEGES;

-- Verify
SHOW GRANTS FOR 'clinic_prod'@'localhost';

EXIT;
```

**⚠️ CRITICAL:** Save password di password manager (1Password, LastPass, Bitwarden)

---

### Step 4: Deploy Application

```bash
# Create web directory
mkdir -p /var/www/clinic.example.com

# Clone dari repository (use deploy key, bukan personal credentials)
cd /var/www/
git clone git@github.com:username/simple-clinic.git clinic.example.com

# Checkout production tag
cd clinic.example.com
git checkout v1.0.0

# Set ownership
chown -R www-data:www-data /var/www/clinic.example.com

# Set permissions
find /var/www/clinic.example.com -type d -exec chmod 755 {} \;
find /var/www/clinic.example.com -type f -exec chmod 644 {} \;

# Secure config files
chmod 600 /var/www/clinic.example.com/config/database.php
```

---

### Step 5: Configure Database Connection

```bash
# Edit config
nano /var/www/clinic.example.com/config/database.php
```

```php
<?php
// PRODUCTION Database Configuration
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'simple_clinic');
define('DB_USER', 'clinic_prod');
define('DB_PASS', 'CHANGE_THIS_VERY_STRONG_PASSWORD_2025!@#$%');  // Password dari Step 3
define('DB_CHARSET', 'utf8mb4');

// Production PDO options
$pdo_options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_PERSISTENT => true,  // Connection pooling
];
```

---

### Step 6: Import Database

```bash
# Import schema
mysql -u clinic_prod -p simple_clinic < /var/www/clinic.example.com/database/schema.sql

# Verify import
mysql -u clinic_prod -p simple_clinic -e "SHOW TABLES;"

# Verify seeded data
mysql -u clinic_prod -p simple_clinic -e "SELECT COUNT(*) FROM users;"
```

---

### Step 7: Change Default Passwords

```bash
# Generate new secure password
php /var/www/clinic.example.com/generate_password.php
# Input: YOUR_NEW_SECURE_PASSWORD_2025
# Output: $2y$10$abcd1234efgh5678...

# Update di database
mysql -u clinic_prod -p simple_clinic
```

```sql
-- Update admin password
UPDATE users 
SET password = '$2y$10$abcd1234efgh5678...'  -- Hash dari generate_password.php
WHERE username = 'admin';

-- Update doctor password
UPDATE users 
SET password = '$2y$10$xyz9876lmno4321...'   -- Hash lain untuk doctor
WHERE username = 'dokter';

-- Verify
SELECT username, LENGTH(password) as password_length FROM users;
-- Should show: password_length = 60 (bcrypt hash)
```

**⚠️ Save passwords ke password manager!**

---

### Step 8: Configure Nginx

```bash
nano /etc/nginx/sites-available/clinic.example.com
```

**Production Nginx config:**

```nginx
# Redirect HTTP to HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name clinic.example.com www.clinic.example.com;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

# HTTPS Server
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name clinic.example.com www.clinic.example.com;
    
    root /var/www/clinic.example.com;
    index index.php;
    
    # SSL Configuration (akan diisi oleh Certbot)
    ssl_certificate /etc/letsencrypt/live/clinic.example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/clinic.example.com/privkey.pem;
    
    # SSL Security Settings
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    
    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    
    # Logging
    access_log /var/log/nginx/clinic-access.log;
    error_log /var/log/nginx/clinic-error.log;
    
    # Disable access to hidden files
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }
    
    # Disable access to sensitive files
    location ~ /(config|database|documentation)/.*\.(php|sql|md)$ {
        deny all;
    }
    
    # Main location
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # PHP-FPM
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        
        # PHP security
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 300;
    }
    
    # Static files caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

**Enable site:**
```bash
ln -s /etc/nginx/sites-available/clinic.example.com /etc/nginx/sites-enabled/
nginx -t  # Test config
systemctl reload nginx
```

---

### Step 9: Install SSL Certificate

```bash
# Install Certbot
apt install certbot python3-certbot-nginx -y

# Get certificate
certbot --nginx -d clinic.example.com -d www.clinic.example.com

# Follow prompts:
# - Enter email untuk renewal notifications
# - Agree to Terms of Service: Yes
# - Share email with EFF: Your choice
# - Redirect HTTP to HTTPS: Yes (option 2)

# Test auto-renewal
certbot renew --dry-run

# Certificate auto-renews via cron (check)
systemctl status certbot.timer
```

---

### Step 10: Final Configuration

#### Update session.php untuk HTTPS

```bash
nano /var/www/clinic.example.com/config/session.php
```

```php
// PRODUCTION: Enable all security features
ini_set('session.cookie_secure', 1);      // Require HTTPS
ini_set('session.cookie_httponly', 1);    // Prevent JavaScript access
ini_set('session.use_strict_mode', 1);    // Reject invalid IDs
ini_set('session.cookie_samesite', 'Strict');  // CSRF protection
ini_set('session.gc_maxlifetime', 7200);  // 2 hour lifetime
```

---

#### Create PHP error log directory

```bash
mkdir -p /var/log/php
chown www-data:www-data /var/log/php
chmod 755 /var/log/php
```

---

## 🔐 Security Hardening

### 1. Firewall Configuration

```bash
# Check UFW status
ufw status

# Allow only necessary ports
ufw allow 22/tcp   # SSH
ufw allow 80/tcp   # HTTP (akan redirect ke HTTPS)
ufw allow 443/tcp  # HTTPS

# Block all other incoming
ufw default deny incoming
ufw default allow outgoing

# Enable
ufw enable
```

---

### 2. SSH Hardening

```bash
nano /etc/ssh/sshd_config
```

```bash
# Disable root login
PermitRootLogin no

# Use SSH keys only
PasswordAuthentication no

# Change SSH port (optional)
Port 2222

# Limit users
AllowUsers your_username
```

```bash
systemctl restart sshd
```

---

### 3. Fail2Ban (Prevent Brute Force)

```bash
# Install
apt install fail2ban -y

# Configure
nano /etc/fail2ban/jail.local
```

```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[sshd]
enabled = true
port = 22
logpath = /var/log/auth.log

[nginx-limit-req]
enabled = true
port = http,https
logpath = /var/log/nginx/clinic-error.log
```

```bash
systemctl restart fail2ban
systemctl enable fail2ban
```

---

### 4. Database Security

```bash
mysql -u root -p
```

```sql
-- Remove anonymous users
DELETE FROM mysql.user WHERE User='';

-- Remove test database
DROP DATABASE IF EXISTS test;

-- Ensure root can only login from localhost
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');

FLUSH PRIVILEGES;
```

---

### 5. File Integrity Monitoring (Optional)

```bash
# Install AIDE
apt install aide -y

# Initialize database
aideinit

# Move database
mv /var/lib/aide/aide.db.new /var/lib/aide/aide.db

# Check integrity
aide --check
```

---

## 💾 Backup & Recovery

### Automated Daily Backup Script

```bash
mkdir -p /root/scripts
nano /root/scripts/backup-clinic.sh
```

```bash
#!/bin/bash
# Production Database & Files Backup Script

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/clinic"
WEB_DIR="/var/www/clinic.example.com"
DB_NAME="simple_clinic"
DB_USER="clinic_prod"
DB_PASS="CHANGE_THIS_VERY_STRONG_PASSWORD_2025!@#$%"

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_backup_$DATE.sql.gz

# Backup files
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz $WEB_DIR

# Upload to remote storage (optional - S3, Dropbox, etc)
# aws s3 cp $BACKUP_DIR/db_backup_$DATE.sql.gz s3://your-bucket/backups/

# Keep only last 30 days
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +30 -delete
find $BACKUP_DIR -name "files_backup_*.tar.gz" -mtime +30 -delete

# Log
echo "[$DATE] Backup completed successfully" >> /var/log/clinic-backup.log
```

**Set permissions & schedule:**
```bash
chmod +x /root/scripts/backup-clinic.sh

# Add to crontab (daily at 2 AM)
crontab -e
0 2 * * * /root/scripts/backup-clinic.sh
```

---

### Recovery Procedure

```bash
# Restore database
gunzip < /var/backups/clinic/db_backup_20251208_020000.sql.gz | mysql -u clinic_prod -p simple_clinic

# Restore files
tar -xzf /var/backups/clinic/files_backup_20251208_020000.tar.gz -C /

# Restart services
systemctl restart php8.0-fpm
systemctl restart nginx
```

---

## 📊 Monitoring & Maintenance

### 1. Setup Log Rotation

```bash
nano /etc/logrotate.d/clinic
```

```
/var/log/nginx/clinic-*.log {
    daily
    rotate 30
    compress
    delaycompress
    notifempty
    create 0640 www-data adm
    sharedscripts
    postrotate
        systemctl reload nginx > /dev/null 2>&1
    endscript
}

/var/log/php/error.log {
    daily
    rotate 30
    compress
    notifempty
    create 0640 www-data www-data
}
```

---

### 2. Monitoring Commands

```bash
# Check server resources
htop

# Check disk space
df -h

# Check Nginx status
systemctl status nginx

# Check PHP-FPM status
systemctl status php8.0-fpm

# Check MySQL status
systemctl status mysql

# View recent errors
tail -f /var/log/nginx/clinic-error.log
tail -f /var/log/php/error.log
```

---

### 3. Database Monitoring

```bash
# Active connections
mysql -u root -p -e "SHOW PROCESSLIST;"

# Database size
mysql -u root -p -e "
SELECT table_schema AS 'Database',
       ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
FROM information_schema.TABLES
WHERE table_schema = 'simple_clinic'
GROUP BY table_schema;"

# Slow queries (if enabled)
mysql -u root -p -e "SHOW VARIABLES LIKE 'slow_query_log%';"
```

---

### 4. Performance Optimization

```bash
# Enable PHP OPcache stats
nano /var/www/clinic.example.com/opcache-status.php
```

```php
<?php
// PROTECTED: Only accessible from localhost
if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1') {
    die('Access denied');
}

echo '<pre>';
print_r(opcache_get_status());
echo '</pre>';
```

**Check OPcache:**
```bash
curl http://localhost/opcache-status.php
```

---

## 🐛 Troubleshooting

### Problem 1: 502 Bad Gateway

**Cause:** PHP-FPM not running or socket issue

**Solution:**
```bash
# Check PHP-FPM status
systemctl status php8.0-fpm

# Restart PHP-FPM
systemctl restart php8.0-fpm

# Check socket
ls -la /var/run/php/php8.0-fpm.sock

# Check Nginx error log
tail -50 /var/log/nginx/clinic-error.log
```

---

### Problem 2: Database Connection Failed

**Check:**
```bash
# Test connection manually
php -r "
\$db = new PDO('mysql:host=localhost;dbname=simple_clinic', 'clinic_prod', 'password');
echo 'Connected successfully!';
"
```

**Solution:**
- Verify credentials in `config/database.php`
- Check MySQL running: `systemctl status mysql`
- Check user privileges: `SHOW GRANTS FOR 'clinic_prod'@'localhost';`

---

### Problem 3: Session Issues

**Symptoms:** Login successful tapi redirect ke login lagi

**Check:**
```bash
# Session directory writable?
ls -ld /var/lib/php/sessions
# Should be: drwx-wx-wt

# Clear old sessions
find /var/lib/php/sessions -type f -mtime +1 -delete
```

---

### Problem 4: SSL Certificate Expired

**Check expiry:**
```bash
echo | openssl s_client -servername clinic.example.com -connect clinic.example.com:443 2>/dev/null | openssl x509 -noout -dates
```

**Renew:**
```bash
certbot renew
systemctl reload nginx
```

---

## 📋 Production Checklist

### Pre-Go-Live

- [ ] All default passwords changed
- [ ] SSL certificate installed & verified
- [ ] Firewall configured
- [ ] Database backed up
- [ ] Error logging enabled
- [ ] Monitoring setup
- [ ] Backup script scheduled
- [ ] DNS pointed to production server
- [ ] Load testing completed
- [ ] Security audit passed

### Post-Go-Live

- [ ] Monitor error logs first 24 hours
- [ ] Check backup running automatically
- [ ] Verify SSL auto-renewal working
- [ ] Test all critical paths
- [ ] Document any issues
- [ ] Train end users
- [ ] Provide admin credentials to client
- [ ] Setup support channel

---

## 🚨 Incident Response Plan

### If Site Goes Down

1. **Check server status:**
   ```bash
   systemctl status nginx
   systemctl status php8.0-fpm
   systemctl status mysql
   ```

2. **Check logs immediately:**
   ```bash
   tail -100 /var/log/nginx/clinic-error.log
   tail -100 /var/log/php/error.log
   ```

3. **Restart services:**
   ```bash
   systemctl restart php8.0-fpm
   systemctl restart nginx
   ```

4. **If database corrupted:**
   ```bash
   # Restore from last backup
   gunzip < /var/backups/clinic/db_backup_LATEST.sql.gz | mysql -u clinic_prod -p simple_clinic
   ```

5. **Rollback to previous version if needed:**
   ```bash
   cd /var/www/clinic.example.com
   git checkout v0.9.0  # Previous stable version
   systemctl restart php8.0-fpm
   ```

---

## 📞 Support Contacts

**Emergency Contacts:**
- **System Administrator:** [Name] - [Phone] - [Email]
- **Lead Developer:** [Name] - [Phone] - [Email]
- **Database Admin:** [Name] - [Phone] - [Email]

**Service Providers:**
- **Hosting Provider:** [Provider Name] - [Support URL]
- **SSL Certificate:** Let's Encrypt (auto-renewal)
- **Domain Registrar:** [Registrar] - [Login URL]

---

## 🎉 Deployment Complete!

**Congratulations! Your application is now live in production.**

### Next Steps:

1. Monitor logs closely for first week
2. Gather user feedback
3. Plan for next release cycle
4. Regular security updates
5. Performance optimization

---

**Production Deployment Successful! 🚀**

_Last Updated: December 8, 2025_
