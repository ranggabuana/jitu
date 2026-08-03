# Jaringan Informasi Terpadu - JITU Banjarnegara

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP Version">
  <img src="https://img.shields.io/badge/Vite-7-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite 7">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS 4">
</p>

## 📖 Tentang Proyek

**JITU (Jasa Izin Terpadu Unggulan) Banjarnegara** adalah sistem informasi perizinan terpadu yang dirancang untuk memudahkan masyarakat dan instansi dalam mengurus perizinan secara online. Sistem ini dibangun untuk Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu (DPMPTSP) Kabupaten Banjarnegara.

Sistem ini mendukung otomatisasi pembuatan dokumen berbasis template Word/HTML, integrasi Tanda Tangan Elektronik (TTE) berbasis passphrase aman, alur verifikasi multi-tahap (multi-role), kolaborasi multi-OPD, pencatatan Service Level Agreement (SLA) secara presisi, serta penyediaan kabinet dokumen digital bagi pemohon.

---

## ✨ Fitur Utama

### 🌐 Front-End (Publik)
- **Landing Page** - Halaman utama interaktif yang menampilkan statistik perizinan dan slider informasi.
- **Daftar Layanan Perizinan** - Katalog komprehensif jenis perizinan yang diselenggarakan.
- **Detail Perizinan** - Informasi komprehensif mencakup dasar hukum, persyaratan berkas, prosedur pengajuan, durasi proses (SLA), dan alur validasi.
- **Tracking Pengajuan Terpadu** - Lacak status pengajuan perizinan secara instan menggunakan Nomor Registrasi atau pemindaian QR Code.
- **Verifikasi Dokumen Resmi** - Scan QR Code pada surat rekomendasi atau surat izin untuk memverifikasi keabsahan dokumen di halaman publik.
- **Berita & Pengumuman** - Slider berita dinamis yang dapat dikonfigurasi melalui panel admin.
- **Pengaduan Online** - Formulir pengaduan masyarakat yang terhubung langsung dengan petugas penanganan.
- **Surat Keterangan Miskin (SKM)** - Pengajuan SKM online disertai survei kepuasan masyarakat terintegrasi.
- **Registrasi Akun** - Pendaftaran mandiri bagi pemohon (Perorangan/Swasta) maupun perwakilan instansi Pemerintah.

### 👤 Dashboard Pemohon & Pemerintah
- **Overview Dashboard** - Statistik ringkas terkait pengajuan aktif, perlu perbaikan, disetujui, dan ditolak.
- **Dokumen Saya (Digital Cabinet)** - Tempat penyimpanan berkas digital (KTP, NPWP, NIB, dll) agar dapat digunakan kembali secara cepat pada pengajuan izin berikutnya tanpa upload ulang.
- **Form Pengajuan Interaktif** - Pengisian form dengan dukungan validasi wilayah administratif (Provinsi, Kabupaten, Kecamatan, Kelurahan) dan captcha keamanan.
- **Konfirmasi Status Wajib Pajak (KSWP)** - Validasi otomatis NPWP dan kepatuhan pajak pemohon.
- **Riwayat & Tracking Detail** - Detail status perizinan beserta histori log alur persetujuan dan catatan pengembalian (return log) jika berkas perlu direvisi.
- **Unduh Dokumen Resmi** - Akses unduh surat izin resmi yang telah bertanda tangan elektronik (TTE).

### ⚙️ Dashboard Admin, BO & OPD (Back-End)
- **Manajemen Perizinan** - CRUD layanan perizinan lengkap dengan:
  - *Dynamic Form Builder* untuk merancang kolom isian per jenis izin.
  - Pengaturan template dokumen (Pernyataan, Permohonan, Keabsahan, Rekomendasi, Izin) berbasis DOCX atau HTML.
  - Penentuan alur validasi kustom (multi-step) dan penugasan ke user spesifik.
  - Dukungan izin Multi-OPD untuk rekomendasi teknis yang memerlukan persetujuan lintas sektor.
- **Manajemen Formulir Khusus BO** - Pengisian data teknis oleh tim Back Office (Badan Teknis) sebelum memberikan rekomendasi. Data global dari pemohon otomatis mengalir sebagai default value ke form BO, dan diteruskan ke draf rekomendasi.
- **SLA & Holiday Management** - Penghitungan durasi proses perizinan secara akurat dengan mengabaikan hari Sabtu, Minggu, dan daftar Hari Libur Nasional yang dikonfigurasi dinamis.
- **Log Tanda Tangan Elektronik (TTE)** - Monitoring log aktivitas e-sign/TTE dokumen resmi oleh Kepala Dinas atau pejabat berwenang.
- **Manajemen Database & Backup** - Backup dan restore database serta file aplikasi langsung dari dashboard.
- **Ekspor Laporan** - Laporan statistik SLA dan data pengajuan dalam format Excel.

---

## 🛠️ Teknologi yang Digunakan

### Backend
- **Laravel 12** - PHP Framework modern
- **PHP 8.2+** - Bahasa pemrograman utama
- **MySQL / MariaDB** - Sistem manajemen database
- **Eloquent ORM** - Integrasi database terabstraksi
- **PHPWord (v1.4)** - Pengolah template dokumen Word (`.docx`)
- **Laravel Dompdf (v3.1)** - Rendering dokumen PDF dari HTML
- **Simple QRCode (v4.2)** - Generator QR Code keabsahan dokumen
- **PDFParser** - Pemrosesan struktur dokumen PDF

### Frontend & UI/UX
- **Vite 7** - Build tool dan server pengembangan ultra-cepat
- **Tailwind CSS 4** - Framework utility-first CSS terbaru
- **Alpine.js** - Framework JavaScript ringan untuk interaktivitas modal & dropdown
- **SweetAlert2** - Modul notifikasi dan dialog konfirmasi premium
- **CKEditor 5** - Rich text editor untuk penulisan artikel berita & persyaratan
- **Font Awesome 6** - Pustaka ikon grafis lengkap

---

## 📋 Persyaratan Sistem

- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 18.x
- **npm** >= 9.x
- **MySQL** >= 8.0 atau **MariaDB** >= 10.6
- **Web Server** (Apache/Nginx/Litespeed)
- **Ekstensi PHP Terpasang**:
  - BCMath, Ctype, cURL, DOM, Fileinfo, JSON, Mbstring, OpenSSL, PCRE, PDO, Tokenizer, XML, GD, ZIP (wajib untuk PHPWord).

---

## 🚀 Instalasi & Konfigurasi

### 1. Clone Repository
```bash
git clone https://github.com/your-username/perijinan.git
cd perijinan
```

### 2. Setup Otomatis (Rekomendasi)
Proyek ini menyediakan script setup otomatis satu langkah untuk mempermudah pemasangan:
```bash
composer run setup
```
Script ini akan otomatis melakukan:
1. Pemasangan dependensi PHP (`composer install`).
2. Penyalinan berkas `.env.example` menjadi `.env`.
3. Pembuatan Application Key (`php artisan key:generate`).
4. Migrasi skema database (`php artisan migrate --force`).
5. Pemasangan dependensi Node (`npm install`).
6. Build aset frontend untuk produksi (`npm run build`).

### 3. Konfigurasi Lingkungan (.env)
Sesuaikan pengaturan database dan detail aplikasi di file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perijinan
DB_USERNAME=root
DB_PASSWORD=

# Konfigurasi Path LibreOffice (wajib jika menggunakan template .docx)
LIBREOFFICE_PATH="C:\Program Files\LibOffice\program\soffice.exe"
```

### 4. Seed Data & Pembuatan Symlink
```bash
# Seed data master wilayah, dinas, regulasi, dan akun default
php artisan db:seed

# Impor basis data wilayah administratif Indonesia jika belum terisi
# (gunakan file wilayah_indo.sql ke database Anda)

# Buat tautan storage agar berkas unggahan dapat diakses publik
php artisan storage:link
```

### 5. Jalankan Aplikasi
Gunakan script development bawaan untuk menjalankan server web, queue worker, dan pemantau aset secara bersamaan:
```bash
composer run dev
```
Perintah ini menggunakan `concurrently` untuk memantau:
- **Server:** Laravel Development Server (`http://localhost:8000`)
- **Queue:** Listener antrean pengiriman email dan proses dokumen (`php artisan queue:listen`)
- **Vite:** Server kompilasi aset frontend
- **Logs:** Real-time log monitoring (`php artisan pail`)

---

## 📁 Struktur Database Utama

| Tabel | Deskripsi |
|-------|-----------|
| `users` | Akun pengguna sistem dengan berbagai tingkatan peran (roles). |
| `perijinan` | Master data jenis layanan perizinan dan konfigurasinya. |
| `perijinan_form_fields` | Definisi kolom input dinamis (global, rekom, izin, bo). |
| `perijinan_validation_flows` | Alur validasi multi-step beserta assignment role/user. |
| `perijinan_opd_configs` | Pengaturan template surat rekomendasi dan kop surat per OPD. |
| `data_perijinan` | Data utama berkas pengajuan perizinan pemohon. |
| `data_perijinan_validasi` | Log riwayat persetujuan, catatan, dan tracking SLA per tahapan. |
| `data_perijinan_return_logs` | Log histori pengembalian berkas untuk revisi oleh pemohon. |
| `master_dokumen_pemohons` | Kategori master dokumen persyaratan digital. |
| `user_dokumens` | Berkas digital pemohon yang disimpan di kabinet "Dokumen Saya". |
| `esign_logs` | Catatan log audit penggunaan Tanda Tangan Elektronik (TTE). |
| `holidays` | Hari libur nasional dinamis untuk perhitungan pemotongan SLA. |
| `opd` | Daftar Organisasi Perangkat Daerah / Instansi Teknis. |
| `regulasi` & `jenis_regulasi` | Regulasi hukum landasan operasional perizinan. |
| `pengaduan` & `pengaduan_handlers` | Pengaduan masyarakat beserta pembagian petugas penanganannya. |
| `skm` & `hasil_skm` | Kuisioner kepuasan masyarakat (SKM) beserta jawabannya. |

---

## 🔐 Tingkatan Peran (User Roles)

1. **Pemohon** (Publik / Swasta / Perorangan): Mendaftarkan akun, mengelola berkas digital, mengajukan izin baru, dan melacak proses berkasnya.
2. **Pemerintah** (Instansi Pemerintah): Akun khusus instansi pemerintah untuk mengajukan perizinan kedinasan.
3. **Front Office (FO)**: Memeriksa dan memvalidasi kelengkapan berkas fisik/digital awal sebelum masuk ke tahap teknis.
4. **Back Office (BO) / Operator OPD**: Menangani peninjauan teknis, mengisi formulir khusus BO, dan menyusun draf surat rekomendasi teknis.
5. **Kepala OPD**: Memeriksa draf rekomendasi teknis dan memberikan persetujuan akhir dari dinas terkait.
6. **Verifikator DPMPTSP**: Melakukan pengecekan keselarasan antara berkas pemohon, hasil tinjauan BO, dan surat rekomendasi OPD.
7. **Kadin DPMPTSP (Kepala Dinas)**: Melakukan persetujuan akhir serta membubuhkan Tanda Tangan Elektronik (TTE) pada surat izin resmi.
8. **Admin**: Memiliki kontrol penuh atas sistem, termasuk manajemen pengguna, form builder, backup database, system logs, dan setting hari libur SLA.

---

## 🔒 Keamanan Sistem

- **Secure Passphrase TTE**: Passphrase untuk penandatanganan elektronik diproses langsung dalam memori sesi dan tidak pernah disimpan dalam database maupun media penyimpanan fisik mana pun.
- **Broken Access Control Prevention**: Pengecekan authorization berlapis pada level middleware, route, model policy, dan query scope untuk memastikan berkas hanya dapat diakses oleh pemohon yang bersangkutan dan petugas yang berwenang.
- **Input Sanitization**: Penggunaan HTML Purifier dan query parameterization untuk mencegah serangan SQL Injection dan Cross-Site Scripting (XSS).
- **Session & Captcha Protection**: Captcha dinamis pada form login, pendaftaran, pengaduan, dan survei SKM untuk mencegah bot spamming.

---

## 📚 Layanan Email & Lupa Password

Aplikasi ini dilengkapi dengan layanan email terpusat untuk pengiriman link reset password dan notifikasi status perizinan. Penjelasan detail mengenai arsitektur, konfigurasi SMTP, antrean (queue), dan pemecahan masalah (troubleshooting) dapat diakses pada berkas panduan khusus:
👉 [**EMAIL_SERVICE_DOCUMENTATION.md**](file:///c:/laragon/www/perijinan/EMAIL_SERVICE_DOCUMENTATION.md)

---

## 🤝 Kontribusi

1. Fork repository ini.
2. Buat branch fitur baru (`git checkout -b feature/FiturBaru`).
3. Lakukan komit perubahan (`git commit -m 'Menambahkan FiturBaru'`).
4. Unggah ke branch Anda (`git push origin feature/FiturBaru`).
5. Ajukan Pull Request untuk ditinjau.

---

Dikembangkan untuk **DPMPTSP Kabupaten Banjarnegara**
