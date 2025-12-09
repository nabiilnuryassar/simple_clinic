# 📘 User Guide - Sistem Informasi Klinik Mutiara

Panduan lengkap penggunaan aplikasi untuk Admin dan Dokter.

---

## 🔐 Login ke Sistem

### Akses Aplikasi

1. Buka browser (Chrome/Firefox/Edge)
2. Ketik URL: **http://localhost:8000** atau **http://localhost/simple-clinic/**
3. Anda akan diarahkan ke halaman login

### Kredensial Default

**Admin:**
- Username: `admin`
- Password: `admin123`

**Dokter:**
- Username: `dokter`
- Password: `dokter123`

> ⚠️ **Penting:** Ganti password default setelah login pertama kali untuk keamanan.

---

## 👨‍💼 Panduan untuk Admin

Admin memiliki akses penuh untuk mengelola data master (pasien, dokter) dan membuat antrian kunjungan.

### 1. Dashboard Admin

Setelah login, Anda akan melihat dashboard dengan statistik:
- **Total Pasien Terdaftar** - Jumlah seluruh pasien di database
- **Total Dokter Aktif** - Jumlah dokter yang berstatus aktif
- **Kunjungan Hari Ini** - Pasien yang terdaftar kunjungan hari ini
- **Total Kunjungan** - Akumulasi semua kunjungan

### 2. Kelola Data Pasien

**Menu:** `Data Pasien`

#### Melihat Daftar Pasien
- Klik menu **"Data Pasien"** di sidebar
- Tabel menampilkan: No. RM, Nama, Tanggal Lahir, Jenis Kelamin, No. Telepon, Alamat

#### Menambah Pasien Baru

1. Scroll ke bagian **"Tambah Pasien Baru"**
2. Isi form dengan lengkap:
   - **Nama Lengkap*** - Nama sesuai identitas
   - **Tanggal Lahir*** - Format: DD/MM/YYYY
   - **Jenis Kelamin*** - Pilih Laki-laki atau Perempuan
   - **No. Telepon** - Nomor HP yang bisa dihubungi (opsional)
   - **Alamat*** - Alamat lengkap pasien
3. Klik tombol **"Simpan Data Pasien"**
4. Sistem akan otomatis generate **Nomor Rekam Medis (No. RM)** format: `RM-YYYYMMDD-XXX`

#### Tips:
- Field dengan tanda `*` wajib diisi
- Pastikan No. Telepon menggunakan format Indonesia (08xxx)
- Alamat sebaiknya lengkap untuk keperluan administrasi

### 3. Kelola Data Dokter

**Menu:** `Data Dokter`

#### Melihat Daftar Dokter
- Klik menu **"Data Dokter"** di sidebar
- Tabel menampilkan: Nama, Spesialisasi, No. Telepon, Email, Status

#### Menambah Dokter Baru

1. Scroll ke bagian **"Tambah Dokter Baru"**
2. Isi form dengan lengkap:
   - **Nama Lengkap*** - Nama dokter (tanpa gelar Dr.)
   - **Spesialisasi*** - Contoh: Umum, Gigi, Anak, THT
   - **No. Telepon*** - HP dokter
   - **Email** - Email dokter (opsional)
   - **Username*** - Username untuk login dokter
   - **Password*** - Password akun dokter (minimal 8 karakter)
3. Klik tombol **"Simpan Data Dokter"**
4. Sistem akan otomatis:
   - Hash password dengan bcrypt
   - Create user account dengan role "dokter"
   - Set status dokter sebagai "aktif"

#### Informasi Penting:
- Dokter yang ditambahkan langsung bisa login ke sistem
- Sampaikan username dan password kepada dokter yang bersangkutan
- Password akan di-hash di database untuk keamanan

### 4. Buat Antrian Kunjungan

**Menu:** `Antrian Kunjungan`

#### Melihat Antrian Hari Ini
- Menampilkan daftar pasien yang terdaftar kunjungan hari ini
- Informasi: No. Antrian, No. RM, Nama Pasien, Dokter, Keluhan, Status

#### Membuat Antrian Baru

1. Scroll ke bagian **"Buat Antrian Kunjungan Baru"**
2. Isi form:
   - **Pilih Pasien*** - Pilih dari dropdown (format: No. RM - Nama)
   - **Pilih Dokter*** - Pilih dokter yang tersedia (format: Dr. Nama - Spesialisasi)
   - **Keluhan Utama*** - Keluhan yang disampaikan pasien
3. Klik tombol **"Buat Antrian"**
4. Sistem akan otomatis:
   - Generate nomor antrian harian (1, 2, 3, dst)
   - Set status awal: "menunggu"
   - Catat tanggal dan waktu kunjungan

#### Catatan:
- Nomor antrian reset setiap hari
- Pastikan pasien dan dokter sudah terdaftar sebelum membuat antrian
- Keluhan sebaiknya spesifik untuk membantu dokter

### 5. Logout

- Klik nama Anda di pojok kanan atas
- Klik tombol **"Logout"**
- Anda akan diarahkan kembali ke halaman login

---

## 👨‍⚕️ Panduan untuk Dokter

Dokter dapat melihat daftar pasien yang menunggu dan melakukan pemeriksaan.

### 1. Dashboard Dokter

Setelah login, Anda akan melihat dashboard dengan statistik personal:
- **Total Pasien Hari Ini** - Pasien yang terdaftar ke Anda hari ini
- **Pasien Menunggu** - Pasien dengan status "menunggu"
- **Pasien Selesai Diperiksa** - Pasien dengan status "selesai"

### 2. Melihat Daftar Pasien

**Menu:** `Periksa Pasien`

Halaman ini menampilkan:
- Daftar pasien yang menunggu untuk diperiksa
- Informasi: No. Antrian, No. RM, Nama, Umur, Jenis Kelamin, Keluhan
- Pasien diurutkan berdasarkan nomor antrian (FIFO - First In First Out)

### 3. Melakukan Pemeriksaan

#### Langkah-langkah:

1. **Pilih Pasien** - Klik tombol **"Periksa"** pada pasien pertama di antrian
2. **Form Pemeriksaan** akan muncul dengan informasi:
   - Nama dan No. RM pasien
   - Keluhan yang disampaikan saat pendaftaran

3. **Isi Form Pemeriksaan Lengkap:**

   **a. Anamnesa*** (Wajib)
   - Riwayat penyakit pasien
   - Keluhan detail
   - Kapan mulai terasa
   - Gejala yang menyertai
   
   Contoh:
   ```
   Pasien mengeluh demam sejak 3 hari yang lalu, disertai batuk berdahak 
   berwarna kuning. Demam naik terutama malam hari (38-39°C). Pasien juga 
   merasakan nyeri tenggorokan dan badan lemas.
   ```

   **b. Diagnosa*** (Wajib)
   - Hasil diagnosa penyakit
   - Bisa menggunakan kode ICD-10 jika ada
   
   Contoh:
   ```
   ISPA (Infeksi Saluran Pernapasan Akut)
   Pharyngitis akut
   ```

   **c. Tindakan** (Opsional)
   - Tindakan medis yang dilakukan
   - Pemeriksaan tambahan
   
   Contoh:
   ```
   - Pemeriksaan fisik (inspeksi tenggorokan)
   - Pengukuran suhu tubuh: 38.5°C
   - Auskultasi paru-paru: ronkhi (-), wheezing (-)
   ```

   **d. Resep Obat** (Opsional)
   - Nama obat
   - Dosis
   - Aturan pakai
   
   Contoh:
   ```
   1. Paracetamol 500mg - 3x1 tablet (sesudah makan)
   2. Ambroxol 30mg - 3x1 tablet (sesudah makan)
   3. Loratadine 10mg - 1x1 tablet (malam sebelum tidur)
   4. Vitamin C 500mg - 1x1 tablet
   
   Durasi: 5 hari
   ```

   **e. Catatan Tambahan** (Opsional)
   - Saran untuk pasien
   - Instruksi khusus
   - Jadwal kontrol ulang
   
   Contoh:
   ```
   - Perbanyak istirahat
   - Minum air putih minimal 2 liter/hari
   - Hindari makanan pedas dan berminyak
   - Kontrol ulang jika demam tidak turun dalam 3 hari
   - Segera ke UGD jika sesak napas
   ```

4. **Submit Pemeriksaan**
   - Klik tombol **"Simpan & Selesai Pemeriksaan"**
   - Status kunjungan berubah otomatis menjadi "selesai"
   - Data rekam medis tersimpan di database
   - Pasien tidak lagi muncul di daftar "menunggu"

### 4. Tips untuk Dokter

#### Anamnesa yang Baik:
- Tanyakan riwayat penyakit sebelumnya
- Catat alergi obat jika ada
- Tanyakan riwayat penyakit keluarga jika relevan
- Catat gejala dengan detail (kapan, intensitas, frekuensi)

#### Diagnosa yang Akurat:
- Gunakan terminologi medis yang tepat
- Bisa menggunakan kode ICD-10 untuk standarisasi
- Jika diagnosa awal, bisa tambahkan "suspected" atau "probable"

#### Resep yang Jelas:
- Tulis nama obat lengkap (generic name lebih baik)
- Cantumkan dosis yang tepat
- Aturan pakai harus jelas (sebelum/sesudah makan, pagi/siang/malam)
- Berikan durasi pengobatan

#### Dokumentasi yang Lengkap:
- Semakin detail, semakin baik untuk continuity of care
- Rekam medis bisa dilihat untuk rujukan di masa depan
- Pastikan tidak ada typo terutama di nama obat

### 5. Logout

- Klik **"Logout"** di menu
- Session akan dihapus dan Anda kembali ke halaman login

---

## ❓ FAQ (Frequently Asked Questions)

### Untuk Admin

**Q: Bagaimana jika lupa password admin?**  
A: Hubungi developer/IT untuk reset password melalui database.

**Q: Apakah bisa edit data pasien yang sudah diinput?**  
A: Saat ini fitur edit belum tersedia di versi ini. Data hanya bisa ditambah dan dilihat.

**Q: Nomor RM bisa diubah manual?**  
A: Tidak. Nomor RM di-generate otomatis oleh sistem untuk mencegah duplikasi.

**Q: Bagaimana jika salah pilih dokter saat buat antrian?**  
A: Saat ini belum ada fitur edit antrian. Pastikan data sudah benar sebelum submit.

**Q: Apakah pasien bisa langsung diperiksa tanpa antrian?**  
A: Tidak. Pasien harus didaftarkan dulu di menu Antrian Kunjungan oleh admin.

### Untuk Dokter

**Q: Apakah bisa melihat rekam medis pasien sebelumnya?**  
A: Fitur ini belum tersedia di versi saat ini. Akan ditambahkan di update mendatang.

**Q: Bagaimana jika salah input diagnosa?**  
A: Saat ini tidak ada fitur edit rekam medis. Pastikan data sudah benar sebelum submit. Jika terpaksa, hubungi admin untuk akses database.

**Q: Apakah bisa print rekam medis?**  
A: Fitur print/export PDF belum tersedia di versi ini.

**Q: Kenapa pasien tidak muncul di daftar?**  
A: Pastikan:
   - Pasien sudah didaftarkan oleh admin di menu Antrian
   - Status masih "menunggu" (belum diperiksa dokter lain)
   - Antrian untuk hari ini (tidak bisa lihat antrian hari lain)

**Q: Apakah bisa mengubah password?**  
A: Fitur change password belum tersedia. Hubungi admin untuk reset password.

---

## 🚨 Troubleshooting

### Masalah Umum

#### 1. Tidak Bisa Login

**Gejala:** Muncul pesan "Username atau password salah"

**Solusi:**
- Pastikan username dan password benar (case-sensitive)
- Pastikan akun sudah terdaftar di database
- Clear cookies dan cache browser, coba lagi
- Pastikan database server berjalan
- Check koneksi database di `config/database.php`

#### 2. Halaman Blank/Kosong

**Gejala:** Halaman tampil putih polos

**Solusi:**
- Check error log PHP
- Pastikan semua file PHP tidak ada syntax error
- Aktifkan `display_errors` di `php.ini` untuk development
- Check koneksi database

#### 3. CSS Tidak Muncul

**Gejala:** Halaman tampil tanpa styling

**Solusi:**
- Check apakah file `assets/css/style.css` ada
- Pastikan web server bisa serve static files
- Check path CSS di browser developer tools (F12 > Network)
- Clear cache browser (Ctrl+F5)

#### 4. Session Error

**Gejala:** Logout sendiri atau muncul error session

**Solusi:**
- Pastikan PHP session enabled
- Check folder `session.save_path` writable
- Clear session files di `/tmp` (Linux) atau `C:\Windows\Temp` (Windows)

#### 5. Database Connection Error

**Gejala:** "Koneksi database gagal"

**Solusi:**
- Pastikan MySQL/MariaDB service running
- Check kredensial di `config/database.php`
- Test koneksi manual: `mysql -u nabiil -p`
- Pastikan database `simple_clinic` sudah di-create

---

## 💡 Tips & Tricks

### Untuk Admin

1. **Registrasi Pasien Baru:**
   - Siapkan fotokopi identitas pasien (KTP/KK)
   - Input data dengan teliti untuk menghindari duplikasi
   - Pastikan no. telepon aktif untuk follow-up

2. **Manajemen Antrian:**
   - Buat antrian di pagi hari untuk pasien yang datang
   - Komunikasikan estimasi waktu tunggu ke pasien
   - Prioritaskan pasien darurat jika perlu

3. **Manajemen Dokter:**
   - Update status dokter jadi "nonaktif" jika cuti/libur
   - Ganti password dokter secara berkala untuk keamanan

### Untuk Dokter

1. **Efisiensi Pemeriksaan:**
   - Buka dashboard di pagi hari untuk melihat jumlah pasien
   - Prioritaskan pasien berdasarkan tingkat urgensi
   - Gunakan template untuk kasus yang umum

2. **Dokumentasi:**
   - Gunakan singkatan medis standar
   - Simpan draft di notepad sebelum input ke sistem (backup)
   - Pastikan tidak ada typo di nama obat

3. **Komunikasi:**
   - Jelaskan diagnosa ke pasien dengan bahasa yang mudah dipahami
   - Pastikan pasien mengerti aturan minum obat
   - Berikan edukasi tentang kondisi pasien

---

## 📞 Dukungan Teknis

Jika mengalami masalah teknis yang tidak tercantum di guide ini:

1. **Check Dokumentasi:**
   - `INSTALLATION.md` - Panduan instalasi
   - `TESTING.md` - Testing & troubleshooting
   - `README.md` - Overview sistem

2. **Contact IT Support:**
   - Email: support@klinikx.com (ganti dengan email yang sesuai)
   - Telepon: 08xx-xxxx-xxxx
   - Jam kerja: Senin-Jumat, 08:00-17:00

3. **Informasi yang Dibutuhkan:**
   - Screenshot error (jika ada)
   - Langkah yang sudah dilakukan sebelum error
   - Browser dan versi yang digunakan
   - Pesan error lengkap

---

## 📋 Checklist Harian

### Untuk Admin

**Pagi Hari:**
- [ ] Login dan check dashboard
- [ ] Review daftar pasien hari ini
- [ ] Pastikan semua dokter aktif (update status jika ada yang cuti)
- [ ] Buat antrian untuk pasien yang sudah datang

**Siang Hari:**
- [ ] Monitor jumlah antrian yang sudah selesai
- [ ] Registrasi pasien baru jika ada
- [ ] Backup data (jika ada sistem backup)

**Sore Hari:**
- [ ] Review statistik hari ini
- [ ] Pastikan semua pasien sudah ditangani
- [ ] Logout dari sistem

### Untuk Dokter

**Pagi Hari:**
- [ ] Login dan check dashboard
- [ ] Lihat jumlah pasien yang menunggu
- [ ] Siapkan alat dan ruang pemeriksaan

**Selama Praktik:**
- [ ] Periksa pasien sesuai urutan antrian
- [ ] Dokumentasi lengkap setiap pemeriksaan
- [ ] Update status setelah selesai periksa

**Sore Hari:**
- [ ] Pastikan semua pasien sudah diperiksa
- [ ] Review rekam medis hari ini
- [ ] Logout dari sistem

---

**User Guide Version 1.0**  
_Last Updated: December 8, 2025_

---

💡 **Saran & Feedback:**  
Jika ada saran untuk improvement user guide ini, silakan hubungi tim developer.

**Happy Managing! 🏥**