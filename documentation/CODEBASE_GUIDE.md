# 💻 Codebase Structure Documentation - Sistem Informasi Klinik Mutiara

Dokumentasi lengkap untuk memahami struktur code, fungsi setiap file, dan cara kerja aplikasi.

---

## 📋 Table of Contents

1. [Project Architecture](#project-architecture)
2. [Folder Structure](#folder-structure)
3. [Config Files](#config-files)
4. [Layout Components](#layout-components)
5. [Pages (Views)](#pages-views)
6. [Process Files (Controllers)](#process-files-controllers)
7. [Application Flow](#application-flow)
8. [Security Implementation](#security-implementation)
9. [Code Conventions](#code-conventions)

---

## 🏗️ Project Architecture

### Architecture Pattern: MVC-Like (Simplified)

```
┌─────────────────────────────────────────────────────┐
│                    USER REQUEST                     │
└─────────────────┬───────────────────────────────────┘
                  │
                  ↓
┌─────────────────────────────────────────────────────┐
│               index.php (Gateway)                   │
│  - Check authentication                             │
│  - Route to appropriate page                        │
└─────────────────┬───────────────────────────────────┘
                  │
        ┌─────────┴─────────┐
        ↓                   ↓
┌──────────────┐    ┌──────────────┐
│   PAGES/     │    │   PROCESS/   │
│  (Views)     │◄───│ (Controllers)│
└──────┬───────┘    └──────┬───────┘
       │                   │
       ├─────────┬─────────┤
       ↓         ↓         ↓
┌────────┐  ┌────────┐  ┌────────┐
│ LAYOUT │  │ CONFIG │  │DATABASE│
│Components│  │Helpers │  │ (MySQL)│
└────────┘  └────────┘  └────────┘
```

### Components Explanation

| Component | Purpose | Examples |
|-----------|---------|----------|
| **index.php** | Entry point, router | Redirect based on auth status |
| **config/** | Configuration & utilities | Database, session, helpers |
| **layout/** | Reusable UI components | Header, sidebar, footer |
| **pages/** | Views (user interface) | Login, dashboard, forms |
| **process/** | Backend logic (controllers) | Login handler, CRUD operations |
| **database/** | Database schema & migrations | schema.sql |
| **assets/** | Static files | CSS, images |

---

## 📂 Folder Structure

```
simple-clinic/
│
├── index.php                    # 🚪 Application gateway/entry point
├── generate_password.php        # 🔐 Password hash generator utility
│
├── config/                      # ⚙️ Configuration files
│   ├── database.php            # Database connection (PDO)
│   ├── session.php             # Session management & security
│   ├── helper.php              # Utility functions
│   └── url_helper.php          # URL & CSRF helpers
│
├── layout/                      # 🎨 Reusable UI components
│   ├── header.php              # HTML head, navbar
│   ├── sidebar.php             # Dynamic sidebar menu
│   └── footer.php              # Footer & closing tags
│
├── pages/                       # 📄 Views (User Interface)
│   ├── auth/
│   │   └── login.php           # Login form
│   ├── admin/
│   │   ├── dashboard.php       # Admin dashboard
│   │   ├── pasien.php          # Patient management
│   │   ├── dokter.php          # Doctor management
│   │   └── antrian.php         # Queue management
│   └── doctor/
│       ├── dashboard.php       # Doctor dashboard
│       └── periksa.php         # Patient examination form
│
├── process/                     # 🔄 Backend logic (Controllers)
│   ├── auth_login.php          # Login handler
│   ├── auth_logout.php         # Logout handler
│   ├── admin_add_pasien.php    # Add patient
│   ├── admin_add_dokter.php    # Add doctor
│   ├── admin_add_visit.php     # Add visit queue
│   └── doctor_update_rm.php    # Save medical record
│
├── assets/                      # 🎨 Static files
│   ├── css/
│   │   └── style.css           # Pure CSS styling
│   └── img/                    # Images & logo
│
└── database/                    # 🗄️ Database files
    └── schema.sql              # Database structure & seed data
```

---

## ⚙️ Config Files

### 1. `config/database.php`

**Purpose:** Database connection using PDO with security best practices

**Key Components:**

```php
// Database credentials (constants)
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'simple_clinic');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

**Main Function:**

```php
function get_db_connection()
```
- **Returns:** PDO connection object
- **Error Handling:** Catch PDOException, log error, die with user-friendly message
- **PDO Options:**
  - `ERRMODE_EXCEPTION` - Throw exceptions on errors
  - `FETCH_ASSOC` - Return associative arrays
  - `EMULATE_PREPARES => false` - Use real prepared statements (security)
  - `PERSISTENT => false` - Don't use persistent connections (default)

**Usage Example:**

```php
$db = get_db_connection();
$stmt = $db->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$patient_id]);
$patient = $stmt->fetch();
```

**Security Features:**
- ✅ PDO prepared statements (SQL injection protection)
- ✅ Error logging (don't expose DB errors to users)
- ✅ Connection pooling ready

---

### 2. `config/session.php`

**Purpose:** Session management with modern security standards

**Session Security Settings:**

```php
ini_set('session.cookie_httponly', 1);  // Prevent JavaScript access to cookies
ini_set('session.cookie_secure', 0);     // 0 for HTTP, 1 for HTTPS
ini_set('session.use_strict_mode', 1);   // Reject invalid session IDs
ini_set('session.cookie_samesite', 'Strict'); // CSRF protection
```

**Key Functions:**

| Function | Purpose | Returns |
|----------|---------|---------|
| `regenerate_session()` | Prevent session fixation | void |
| `is_logged_in()` | Check if user is authenticated | boolean |
| `check_role($roles)` | Enforce role-based access control | void (die if unauthorized) |
| `logout()` | Destroy session & clear cookies | void |
| `generate_csrf_token()` | Create CSRF token for forms | string (token) |
| `verify_csrf_token($token)` | Validate CSRF token | boolean |

**Usage Examples:**

```php
// Check if user logged in
if (is_logged_in()) {
    echo "Welcome, " . $_SESSION['nama'];
}

// Protect admin-only page
check_role(['admin']); // Will die if not admin

// CSRF protection in form
<input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

// Validate CSRF in backend
if (!verify_csrf_token($_POST['csrf_token'])) {
    die('CSRF validation failed!');
}
```

**Security Features:**
- ✅ Session fixation protection (regenerate ID)
- ✅ HttpOnly cookies (prevent XSS access)
- ✅ SameSite cookies (CSRF protection)
- ✅ Role-based access control
- ✅ CSRF token validation

---

### 3. `config/helper.php`

**Purpose:** Utility functions used throughout application

**Key Functions:**

#### Input Sanitization
```php
function clean_input($data)
```
- **Purpose:** Sanitize user input to prevent XSS
- **Operations:** trim(), stripslashes(), htmlspecialchars()
- **Returns:** Sanitized string
- **Usage:** `$nama = clean_input($_POST['nama']);`

#### URL Helpers
```php
function base_url($path = '')
```
- **Purpose:** Generate absolute URL
- **Returns:** Full URL (http://localhost:8000/path)
- **Usage:** `<link href="<?php echo base_url('assets/css/style.css'); ?>">`

```php
function redirect($path)
```
- **Purpose:** Redirect to another page
- **Usage:** `redirect('pages/admin/dashboard.php');`

#### Flash Messages
```php
function set_flash($type, $message)
function get_flash()
```
- **Purpose:** Session-based one-time notifications
- **Types:** success, error, warning, info
- **Usage:**
  ```php
  set_flash('success', 'Data berhasil disimpan!');
  // Next page load:
  $flash = get_flash(); // Returns: ['type' => 'success', 'message' => '...']
  ```

#### Date Formatting
```php
function format_date($date)
```
- **Purpose:** Format date to Indonesian format
- **Input:** YYYY-MM-DD
- **Output:** DD Bulan YYYY (e.g., "08 Desember 2025")

#### Password Functions
```php
function hash_password($password)
```
- **Purpose:** Hash password using bcrypt
- **Returns:** 60-character hash
- **Usage:** `$hashed = hash_password('admin123');`

```php
function verify_password($password, $hash)
```
- **Purpose:** Verify password against hash
- **Returns:** boolean
- **Usage:** `if (verify_password($input_pass, $stored_hash)) { ... }`

#### Email Validation
```php
function is_valid_email($email)
```
- **Purpose:** Validate email format
- **Returns:** boolean
- **Usage:** `if (is_valid_email($email)) { ... }`

---

### 4. `config/url_helper.php`

**Purpose:** Additional URL and CSRF helper functions (extension of helper.php)

**Functions:**
- URL generation
- CSRF token management
- Authentication check utilities

---

## 🎨 Layout Components

### 1. `layout/header.php`

**Purpose:** HTML head, meta tags, CSS links, and navigation bar

**Structure:**
```php
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title ?? 'Klinik Mutiara'; ?></title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-brand">🏥 Klinik Mutiara</div>
        <div class="navbar-menu">
            <span><?php echo $_SESSION['nama']; ?></span>
            <a href="<?php echo base_url('process/auth_logout.php'); ?>">Logout</a>
        </div>
    </nav>
```

**Usage in Pages:**
```php
<?php
$page_title = 'Dashboard Admin';
require_once __DIR__ . '/../../layout/header.php';
?>
```

**Features:**
- ✅ Dynamic page title
- ✅ CSS loading with base_url()
- ✅ Responsive meta viewport
- ✅ UTF-8 encoding

---

### 2. `layout/sidebar.php`

**Purpose:** Dynamic sidebar menu based on user role

**Logic:**
```php
if ($_SESSION['role'] === 'admin') {
    // Show admin menu: Dashboard, Pasien, Dokter, Antrian
} elseif ($_SESSION['role'] === 'dokter') {
    // Show doctor menu: Dashboard, Periksa Pasien
}
```

**Features:**
- ✅ Role-based menu items
- ✅ Active menu highlighting
- ✅ Icon support
- ✅ Clean navigation structure

**Usage:**
```php
require_once __DIR__ . '/../../layout/sidebar.php';
```

---

### 3. `layout/footer.php`

**Purpose:** Closing HTML tags and footer content

**Structure:**
```php
    <footer class="footer">
        <p>&copy; 2025 Klinik Mutiara. All rights reserved.</p>
    </footer>
</body>
</html>
```

**Usage:**
```php
require_once __DIR__ . '/../../layout/footer.php';
```

---

## 📄 Pages (Views)

### Auth Module

#### `pages/auth/login.php`

**Purpose:** Login form with CSRF protection

**Flow:**
1. Check if already logged in → redirect to dashboard
2. Generate CSRF token
3. Display login form
4. Submit to `process/auth_login.php`

**Key Features:**
- ✅ CSRF token in hidden field
- ✅ Auto-redirect if already logged in
- ✅ Client-side validation (required fields)
- ✅ Clean UI with centered card

**Form Structure:**
```html
<form method="POST" action="process/auth_login.php">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    <input type="text" name="username" required>
    <input type="password" name="password" required>
    <button type="submit">Login</button>
</form>
```

---

### Admin Module

#### `pages/admin/dashboard.php`

**Purpose:** Admin dashboard with statistics

**Access Control:**
```php
check_role(['admin']); // Only admin can access
```

**Statistics Displayed:**
- Total pasien (SELECT COUNT(*) FROM patients)
- Total dokter (SELECT COUNT(*) FROM doctors)
- Kunjungan hari ini (WHERE DATE(tanggal_kunjungan) = CURDATE())
- Total kunjungan (SELECT COUNT(*) FROM visits)

**Features:**
- ✅ Real-time statistics from database
- ✅ Card-based layout
- ✅ Color-coded stat cards
- ✅ Error handling with try-catch

---

#### `pages/admin/pasien.php`

**Purpose:** Patient management (CRUD)

**Features:**
1. **List Patients:** Table with all patients
2. **Add Patient Form:**
   - Auto-generate RM number (RM-YYYYMMDD-XXX)
   - Input: nama, tanggal_lahir, jenis_kelamin, no_telepon, alamat
   - CSRF protection
   - Submit to `process/admin_add_pasien.php`

**Table Structure:**
```html
<table>
    <thead>
        <tr>
            <th>No RM</th>
            <th>Nama</th>
            <th>Tanggal Lahir</th>
            <th>Jenis Kelamin</th>
            <th>No Telp</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $stmt->fetch()): ?>
        <tr>
            <td><?php echo $row['no_rm']; ?></td>
            <!-- ... -->
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
```

---

#### `pages/admin/dokter.php`

**Purpose:** Doctor management (CRUD)

**Features:**
1. **List Doctors:** Table with all doctors
2. **Add Doctor Form:**
   - Input: nama, spesialisasi, no_telepon, email, status
   - Dropdown for status (aktif/nonaktif)
   - Submit to `process/admin_add_dokter.php`

**Status Badge:**
```php
<?php if ($row['status'] === 'aktif'): ?>
    <span class="badge badge-success">Aktif</span>
<?php else: ?>
    <span class="badge badge-danger">Non-aktif</span>
<?php endif; ?>
```

---

#### `pages/admin/antrian.php`

**Purpose:** Queue management (add visit)

**Features:**
1. **Today's Queue List:**
   - Table showing: no_antrian, nama_pasien, nama_dokter, keluhan, status
   - Status badge (menunggu/selesai/batal)

2. **Add Visit Form:**
   - Dropdown: Select patient (from patients table)
   - Dropdown: Select doctor (only aktif doctors)
   - Auto-generate queue number (next number today)
   - Input: keluhan (textarea)
   - Submit to `process/admin_add_visit.php`

**Queue Number Logic:**
```php
// Get max queue number today
$stmt = $db->prepare("SELECT IFNULL(MAX(no_antrian), 0) + 1 as next_no FROM visits WHERE DATE(tanggal_kunjungan) = CURDATE()");
$stmt->execute();
$next_no = $stmt->fetch()['next_no'];
```

---

### Doctor Module

#### `pages/doctor/dashboard.php`

**Purpose:** Doctor dashboard with personal statistics

**Access Control:**
```php
check_role(['dokter']); // Only dokter can access
```

**Statistics (Filtered by doctor_id):**
- Total pasien hari ini
- Pasien menunggu
- Pasien selesai diperiksa

**SQL Example:**
```sql
SELECT COUNT(*) FROM visits 
WHERE doctor_id = ? 
  AND DATE(tanggal_kunjungan) = CURDATE() 
  AND status = 'menunggu'
```

---

#### `pages/doctor/periksa.php`

**Purpose:** Patient examination and medical record form

**Features:**

1. **Waiting Queue Table:**
   - Show patients with status 'menunggu'
   - Filtered by current doctor
   - Click patient to open examination form

2. **Examination Form:**
   - Display patient info (read-only)
   - Input fields:
     - Anamnesa (TEXT) - Riwayat penyakit
     - Diagnosa (TEXT) - Hasil diagnosa
     - Tindakan (TEXT) - Tindakan medis (optional)
     - Resep (TEXT) - Resep obat (optional)
     - Catatan (TEXT) - Catatan tambahan (optional)
   - Submit to `process/doctor_update_rm.php`

**Form Structure:**
```html
<form method="POST" action="process/doctor_update_rm.php">
    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
    <input type="hidden" name="visit_id" value="<?php echo $visit_id; ?>">
    
    <textarea name="anamnesa" required></textarea>
    <textarea name="diagnosa" required></textarea>
    <textarea name="tindakan"></textarea>
    <textarea name="resep"></textarea>
    <textarea name="catatan"></textarea>
    
    <button type="submit">Simpan Rekam Medis</button>
</form>
```

---

## 🔄 Process Files (Controllers)

### Auth Processes

#### `process/auth_login.php`

**Purpose:** Handle login form submission

**Flow:**
```
1. Check method is POST
2. Validate CSRF token
3. Sanitize username input
4. Query user from database (prepared statement)
5. Verify password (bcrypt)
6. If valid:
   - Regenerate session ID
   - Set session variables (user_id, username, nama, role)
   - Redirect based on role (admin → admin dashboard, dokter → doctor dashboard)
7. If invalid:
   - Set flash error message
   - Redirect back to login
```

**Security Features:**
- ✅ CSRF validation
- ✅ Prepared statements (SQL injection protection)
- ✅ Password verification with bcrypt
- ✅ Session regeneration (prevent session fixation)
- ✅ Input sanitization

**Code Example:**
```php
// Validate CSRF
if (!verify_csrf_token($_POST['csrf_token'])) {
    set_flash('error', 'Invalid CSRF token');
    redirect('pages/auth/login.php');
}

// Query user
$stmt = $db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
$stmt->execute([$username]);
$user = $stmt->fetch();

// Verify password
if ($user && verify_password($password, $user['password'])) {
    // Login successful
    regenerate_session();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    // ...
}
```

---

#### `process/auth_logout.php`

**Purpose:** Handle logout

**Flow:**
```
1. Destroy session variables
2. Delete session cookie
3. Destroy session
4. Redirect to login page
```

**Code:**
```php
logout(); // From session.php
set_flash('success', 'Anda telah logout.');
redirect('pages/auth/login.php');
```

---

### Admin Processes

#### `process/admin_add_pasien.php`

**Purpose:** Handle add patient form submission

**Flow:**
```
1. Check method is POST
2. Validate CSRF token
3. Sanitize all inputs
4. Validate required fields
5. Auto-generate RM number (RM-YYYYMMDD-XXX)
6. Insert to database (prepared statement)
7. Set flash success message
8. Redirect back to pasien page
```

**RM Number Generation:**
```php
// Get max RM today
$stmt = $db->prepare("SELECT MAX(no_rm) as max_rm FROM patients WHERE DATE(created_at) = CURDATE()");
$stmt->execute();
$result = $stmt->fetch();

// Generate next RM
if ($result['max_rm']) {
    // Extract number and increment
    $parts = explode('-', $result['max_rm']);
    $next_no = str_pad((int)$parts[2] + 1, 3, '0', STR_PAD_LEFT);
} else {
    $next_no = '001';
}

$no_rm = 'RM-' . date('Ymd') . '-' . $next_no;
```

**Insert Query:**
```php
$stmt = $db->prepare("
    INSERT INTO patients (no_rm, nama, tanggal_lahir, jenis_kelamin, no_telepon, alamat) 
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->execute([$no_rm, $nama, $tanggal_lahir, $jenis_kelamin, $no_telepon, $alamat]);
```

---

#### `process/admin_add_dokter.php`

**Purpose:** Handle add doctor form submission

**Flow:**
```
1. Validate CSRF & method
2. Sanitize inputs
3. Validate email format
4. Insert to database
5. Redirect with flash message
```

**Validation:**
```php
if (!is_valid_email($email)) {
    set_flash('error', 'Format email tidak valid');
    redirect('pages/admin/dokter.php');
}
```

---

#### `process/admin_add_visit.php`

**Purpose:** Handle add visit queue form submission

**Flow:**
```
1. Validate CSRF & method
2. Sanitize inputs
3. Auto-generate queue number (today's next number)
4. Insert to visits table
5. Redirect with flash message
```

**Queue Number Logic:**
```php
// Get next queue number today
$stmt = $db->prepare("
    SELECT IFNULL(MAX(no_antrian), 0) + 1 as next_no 
    FROM visits 
    WHERE DATE(tanggal_kunjungan) = CURDATE()
");
$stmt->execute();
$next_no = $stmt->fetch()['next_no'];
```

---

### Doctor Processes

#### `process/doctor_update_rm.php`

**Purpose:** Save medical record after examination

**Flow:**
```
1. Validate CSRF & method
2. Check user is dokter role
3. Sanitize all inputs
4. Validate required fields (anamnesa, diagnosa)
5. Transaction start:
   a. Insert medical_record
   b. Update visit status to 'selesai'
6. Transaction commit
7. Redirect with success message
```

**Transaction Example:**
```php
try {
    $db->beginTransaction();
    
    // Insert medical record
    $stmt = $db->prepare("
        INSERT INTO medical_records 
        (visit_id, patient_id, doctor_id, tanggal_periksa, anamnesa, diagnosa, tindakan, resep, catatan) 
        VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$visit_id, $patient_id, $doctor_id, $anamnesa, $diagnosa, $tindakan, $resep, $catatan]);
    
    // Update visit status
    $stmt = $db->prepare("UPDATE visits SET status = 'selesai' WHERE id = ?");
    $stmt->execute([$visit_id]);
    
    $db->commit();
    set_flash('success', 'Rekam medis berhasil disimpan!');
    
} catch (PDOException $e) {
    $db->rollBack();
    set_flash('error', 'Gagal menyimpan rekam medis: ' . $e->getMessage());
}
```

**Why Transaction?**
- Ensure both operations succeed or fail together
- Maintain data consistency
- If medical record insert fails, visit status won't change

---

## 🚦 Application Flow

### Login Flow

```
User visits index.php
  ↓
Not logged in?
  ↓
Redirect to pages/auth/login.php
  ↓
User submits form
  ↓
POST to process/auth_login.php
  ↓
Validate credentials
  ↓
Success? Set session & redirect to dashboard
Fail? Flash error & redirect back to login
```

### Admin Add Patient Flow

```
Admin at pages/admin/pasien.php
  ↓
Fill form & submit
  ↓
POST to process/admin_add_pasien.php
  ↓
Validate CSRF & inputs
  ↓
Generate RM number
  ↓
Insert to database
  ↓
Flash success message
  ↓
Redirect back to pages/admin/pasien.php
```

### Doctor Examination Flow

```
Doctor at pages/doctor/periksa.php
  ↓
View waiting patients (status = 'menunggu')
  ↓
Select patient & fill examination form
  ↓
POST to process/doctor_update_rm.php
  ↓
Validate inputs
  ↓
START TRANSACTION
  ├─ Insert medical_record
  └─ Update visit status = 'selesai'
COMMIT
  ↓
Flash success & redirect back
```

---

## 🔐 Security Implementation

### 1. SQL Injection Protection

**Method:** PDO Prepared Statements

```php
// ❌ VULNERABLE (SQL Injection)
$query = "SELECT * FROM users WHERE username = '$username'";

// ✅ SECURE (Prepared Statement)
$stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
```

### 2. XSS Protection

**Method:** Input Sanitization & Output Escaping

```php
// Input sanitization
$nama = clean_input($_POST['nama']); // htmlspecialchars()

// Output escaping
echo clean_input($user['nama']); // Always escape when displaying
```

### 3. CSRF Protection

**Method:** Token Validation

```php
// Generate token in form
<input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

// Validate in backend
if (!verify_csrf_token($_POST['csrf_token'])) {
    die('CSRF validation failed');
}
```

### 4. Password Security

**Method:** Bcrypt Hashing

```php
// Hash password (registration/update)
$hashed = password_hash($password, PASSWORD_BCRYPT); // 60 chars

// Verify password (login)
if (password_verify($input_password, $stored_hash)) {
    // Valid
}
```

### 5. Session Security

**Methods:**
- HttpOnly cookies (prevent JavaScript access)
- SameSite cookies (CSRF protection)
- Session regeneration (prevent fixation)
- Role-based access control

```php
// Check role before accessing page
check_role(['admin']); // Dies if not admin
```

### 6. Authentication Flow Security

```php
// Prevent direct access to pages
if (!is_logged_in()) {
    redirect('pages/auth/login.php');
}

// Prevent direct access to controllers
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/auth/login.php');
}
```

---

## 📝 Code Conventions

### Naming Conventions

**Variables:** `snake_case`
```php
$nama_pasien = "John Doe";
$total_kunjungan = 10;
```

**Functions:** `snake_case`
```php
function get_db_connection() { }
function clean_input($data) { }
```

**Files:** `snake_case` with descriptive names
```php
admin_add_pasien.php
doctor_update_rm.php
```

### File Structure

**View Files (pages/):**
```php
<?php
$page_title = 'Page Title';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/helper.php';
require_once __DIR__ . '/../../config/database.php';

check_role(['admin']); // Access control

// Database queries
$db = get_db_connection();
// ...

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<!-- HTML content -->

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
```

**Controller Files (process/):**
```php
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/helper.php';

// Check method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/auth/login.php');
}

// Validate CSRF
if (!verify_csrf_token($_POST['csrf_token'])) {
    set_flash('error', 'Invalid CSRF token');
    redirect('...');
}

// Process logic
try {
    $db = get_db_connection();
    // Database operations
    
    set_flash('success', 'Success message');
    redirect('pages/...');
    
} catch (PDOException $e) {
    error_log($e->getMessage());
    set_flash('error', 'Error message');
    redirect('pages/...');
}
```

### Comments

```php
// Single-line for brief explanation

/**
 * Multi-line for function documentation
 * 
 * @param string $data Input data to sanitize
 * @return string Sanitized data
 */
function clean_input($data) { }
```

---

## 🔧 Special Files

### `index.php`

**Purpose:** Application gateway/router

**Logic:**
```php
if (is_logged_in()) {
    // Redirect based on role
    if ($_SESSION['role'] === 'admin') {
        redirect('pages/admin/dashboard.php');
    } else {
        redirect('pages/doctor/dashboard.php');
    }
} else {
    // Not logged in
    redirect('pages/auth/login.php');
}
```

**Why This Pattern?**
- Single entry point
- Centralized routing logic
- Prevents unauthorized access to root URL

---

### `generate_password.php`

**Purpose:** Utility script to generate bcrypt password hashes

**Usage:**
```bash
php generate_password.php
```

**Output:**
```
Admin Password: admin123
Hash: $2y$10$DZc/PUgPtGibOyqpzHCm0efwDARhyIExMjGzGe5c0g37xFh.Ey562

Dokter Password: dokter123
Hash: $2y$10$fM/H5Xpw7TZM2uilOW9E4u/t1TrCWhtY0IzfZeArQQtxdynKRrdqu
```

**Why Separate Script?**
- Password hashing is slow (intentionally, for security)
- Generate hashes offline, copy to SQL
- Don't hash passwords in database seeding (would rehash on every import)

**Important:** Delete or secure this file in production!

---

## 📚 Key Takeaways

### Architecture
✅ MVC-like pattern (Pages = Views, Process = Controllers, Config = Models)
✅ Separation of concerns
✅ Reusable components (layout/)
✅ Centralized configuration (config/)

### Security
✅ PDO prepared statements (SQL injection protection)
✅ CSRF tokens on all forms
✅ XSS protection with htmlspecialchars()
✅ Bcrypt password hashing
✅ Session security (httponly, samesite)
✅ Role-based access control

### Best Practices
✅ Consistent naming (snake_case)
✅ Error handling with try-catch
✅ Flash messages for user feedback
✅ Code comments for clarity
✅ DRY principle (helper functions)

### Database
✅ Foreign key relationships
✅ Cascade deletes
✅ Indexes for performance
✅ Transactions for data integrity

---

**Codebase Documentation Complete! 💻**

_Last Updated: December 8, 2025_
