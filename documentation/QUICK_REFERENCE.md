# 🎯 Quick Reference Card - Sistem Informasi Klinik Mutiara

Cheat sheet untuk command dan konfigurasi yang sering digunakan.

---

## 🛠️ Development Commands

### Database
```bash
# Create database
mysql -u root -p -e "CREATE DATABASE simple_clinic;"

# Import schema
mysql -u root -p simple_clinic < database/schema.sql

# Backup database
mysqldump -u root -p simple_clinic > backup.sql

# Restore database
mysql -u root -p simple_clinic < backup.sql

# Check tables
mysql -u root -p simple_clinic -e "SHOW TABLES;"

# View users
mysql -u root -p simple_clinic -e "SELECT username, nama, role FROM users;"
```

---

### PHP Server
```bash
# Start PHP built-in server
php -S localhost:8000

# Start on custom port
php -S localhost:8080

# Start on all interfaces
php -S 0.0.0.0:8000

# Run in background
php -S localhost:8000 > /dev/null 2>&1 &

# Stop background server
pkill -f "php -S"
```

---

### Git Workflow
```bash
# Clone repository
git clone <repository-url>

# Create feature branch
git checkout -b feature/new-feature

# Stage all changes
git add .

# Commit with message
git commit -m "Feature: Add patient search"

# Push to remote
git push origin feature/new-feature

# Tag version
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0

# Check current branch
git branch

# View commit history
git log --oneline
```

---

## 🧪 Testing Commands

### Quick Health Check
```bash
# Test PHP version
php -v

# Test database connection
php -r "require 'config/database.php'; \$db = get_db_connection(); echo 'Connected!';"

# Test web server
curl -I http://localhost:8000/

# Test login page
curl http://localhost:8000/pages/auth/login.php

# Test CSS loading
curl -I http://localhost:8000/assets/css/style.css
```

---

### Generate Password
```bash
# Generate bcrypt hash
php generate_password.php

# Input: your_password
# Output: $2y$10$abc123...
```

---

## 🚀 Production Commands

### Server Management
```bash
# Check service status
systemctl status nginx
systemctl status php8.0-fpm
systemctl status mysql

# Restart services
systemctl restart nginx
systemctl restart php8.0-fpm
systemctl restart mysql

# View logs
tail -f /var/log/nginx/clinic-error.log
tail -f /var/log/php/error.log
tail -f /var/log/mysql/error.log
```

---

### SSL Certificate
```bash
# Install Certbot
apt install certbot python3-certbot-nginx -y

# Get certificate
certbot --nginx -d clinic.example.com

# Renew certificate
certbot renew

# Test renewal
certbot renew --dry-run
```

---

### Backup & Restore
```bash
# Daily backup script
mysqldump -u clinic_prod -p simple_clinic | gzip > backup_$(date +%Y%m%d).sql.gz

# Restore from backup
gunzip < backup_20251208.sql.gz | mysql -u clinic_prod -p simple_clinic

# Backup files
tar -czf clinic_backup_$(date +%Y%m%d).tar.gz /var/www/clinic

# Restore files
tar -xzf clinic_backup_20251208.tar.gz -C /
```

---

## ⚙️ Configuration Files

### Database Configuration
**File:** `config/database.php`
```php
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'simple_clinic');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

---

### Session Configuration
**File:** `config/session.php`
```php
// Development
ini_set('session.cookie_secure', 0);  // HTTP OK

// Production
ini_set('session.cookie_secure', 1);  // HTTPS only
```

---

### PHP Settings (Development)
**File:** `index.php` (add at top)
```php
// Show errors
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

---

### PHP Settings (Production)
**File:** `/etc/php/8.0/fpm/php.ini`
```ini
display_errors = Off
log_errors = On
error_log = /var/log/php/error.log
expose_php = Off
```

---

## 🔐 Security Commands

### File Permissions
```bash
# Set proper permissions
find . -type d -exec chmod 755 {} \;  # Directories
find . -type f -exec chmod 644 {} \;  # Files
chmod 600 config/database.php         # Config files

# Set ownership
chown -R www-data:www-data /var/www/clinic
```

---

### Firewall (UFW)
```bash
# Enable firewall
ufw enable

# Allow SSH
ufw allow 22/tcp

# Allow HTTP/HTTPS
ufw allow 80/tcp
ufw allow 443/tcp

# Check status
ufw status

# Delete rule
ufw delete allow 80/tcp
```

---

### Fail2Ban
```bash
# Check banned IPs
fail2ban-client status

# Unban IP
fail2ban-client set sshd unbanip 192.168.1.100

# Check jail status
fail2ban-client status sshd
```

---

## 📊 Monitoring Commands

### System Resources
```bash
# CPU and memory usage
htop

# Disk space
df -h

# Disk usage by directory
du -sh /var/www/clinic/*

# Check running processes
ps aux | grep php
ps aux | grep nginx
```

---

### Database Monitoring
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

# Slow queries
mysql -u root -p -e "SHOW VARIABLES LIKE 'slow_query%';"
```

---

### Nginx Monitoring
```bash
# Check configuration
nginx -t

# Reload configuration
systemctl reload nginx

# Access log (last 100 lines)
tail -100 /var/log/nginx/clinic-access.log

# Error log (real-time)
tail -f /var/log/nginx/clinic-error.log

# Count requests
wc -l /var/log/nginx/clinic-access.log
```

---

## 🆘 Troubleshooting Quick Fixes

### Database Connection Failed
```bash
# Check MySQL running
systemctl status mysql

# Check user privileges
mysql -u root -p -e "SHOW GRANTS FOR 'clinic_prod'@'localhost';"

# Reset user password
mysql -u root -p -e "ALTER USER 'clinic_prod'@'localhost' IDENTIFIED BY 'new_password';"
```

---

### CSS Not Loading
```bash
# Check file exists
ls -la assets/css/style.css

# Check permissions
chmod 644 assets/css/style.css

# Test direct access
curl -I http://localhost:8000/assets/css/style.css
```

---

### 502 Bad Gateway
```bash
# Check PHP-FPM running
systemctl status php8.0-fpm

# Restart PHP-FPM
systemctl restart php8.0-fpm

# Check socket
ls -la /var/run/php/php8.0-fpm.sock
```

---

### Session Not Working
```bash
# Check session directory
ls -ld /var/lib/php/sessions

# Fix permissions
chmod 1733 /var/lib/php/sessions

# Clear old sessions
find /var/lib/php/sessions -type f -mtime +1 -delete
```

---

### 500 Internal Server Error
```bash
# Check PHP error log
tail -50 /var/log/php/error.log

# Check Nginx error log
tail -50 /var/log/nginx/clinic-error.log

# Check Apache error log (if using Apache)
tail -50 /var/log/apache2/error.log
```

---

## 📋 Default Credentials

### Development
| Component | Username | Password | Note |
|-----------|----------|----------|------|
| MySQL | `root` | (empty) | XAMPP default |
| Admin | `admin` | `admin123` | Change in production! |
| Dokter | `dokter` | `dokter123` | Change in production! |

### Production
| Component | Username | Password | Note |
|-----------|----------|----------|------|
| MySQL | `clinic_prod` | *secure password* | Set during setup |
| Admin | `admin` | *change this!* | Generate with bcrypt |
| Dokter | `dokter` | *change this!* | Generate with bcrypt |

---

## 🔗 Important URLs

### Development
- Application: http://localhost:8000
- Login: http://localhost:8000/pages/auth/login.php
- phpMyAdmin: http://localhost/phpmyadmin (XAMPP)

### Production
- Application: https://clinic.example.com
- Admin Panel: https://clinic.example.com/pages/admin/dashboard.php
- Doctor Panel: https://clinic.example.com/pages/doctor/dashboard.php

---

## 📞 Documentation Links

| Documentation | Purpose |
|---------------|---------|
| [README.md](../README.md) | Project overview |
| [DEV_SETUP.md](DEV_SETUP.md) | Development setup |
| [STAGING_DEPLOY.md](STAGING_DEPLOY.md) | Staging deployment |
| [PRODUCTION_DEPLOY.md](PRODUCTION_DEPLOY.md) | Production deployment |
| [USER_GUIDE.md](USER_GUIDE.md) | User manual |
| [TESTING.md](TESTING.md) | Testing guide |

---

**Keep this card handy! 📌**

_Last Updated: December 8, 2025_
