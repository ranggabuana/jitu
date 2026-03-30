# Sistem Informasi Perizinan Terpadu - JITU Banjarnegara

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP Version">
  <img src="https://img.shields.io/badge/Vite-5-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite 5">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
</p>

## 📖 Tentang Proyek

**JITU (Jasa Izin Terpadu Unggulan) Banjarnegara** adalah sistem informasi perizinan terpadu yang dirancang untuk memudahkan masyarakat dalam mengurus perizinan secara online. Sistem ini dibangun untuk Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu (DPMPTSP) Kabupaten Banjarnegara.

### ✨ Fitur Utama

#### 🌐 Front-End (Publik)
- **Landing Page** - Halaman depan dengan informasi layanan
- **Daftar Layanan Perizinan** - Katalog lengkap jenis perizinan yang tersedia
- **Detail Perizinan** - Informasi lengkap tentang:
  - Dasar hukum
  - Persyaratan
  - Prosedur pengajuan
  - Formulir yang harus diisi
  - Alur validasi
- **Berita & Informasi** - Berita terbaru dengan fitur slider
- **Pengaduan Online** - Form pengaduan masyarakat
- **Surat Keterangan Miskin (SKM)** - Pengajuan SKM online
- **Registrasi Pemohon** - Pendaftaran akun untuk pemohon perizinan

#### 👤 Dashboard Pemohon
- **Dashboard** - Ringkasan pengajuan perizinan
- **Daftar Perizinan** - Katalog perizinan dengan modal detail
- **Pengajuan Izin Baru** - Form pengajuan perizinan online
- **Tracking Pengajuan** - Pantau status pengajuan
- **Profil User** - Kelola data pribadi
- **Riwayat Pengajuan** - Daftar semua pengajuan yang pernah dibuat

#### 🔐 Dashboard Admin
- **Dashboard** - Statistik dan overview sistem
- **Manajemen Perijinan** - CRUD layanan perizinan dengan:
  - Form builder dinamis
  - Alur validasi kustom
  - Pengelolaan persyaratan
- **Manajemen Berita** - CRUD berita dengan:
  - Toggle slider homepage
  - Upload gambar
  - Rich text editor (CKEditor 5)
  - Status aktif/tidak aktif
- **Manajemen OPD** - Kelola Organisasi Perangkat Daerah
- **Manajemen Pemohon** - Kelola akun pemohon
- **Manajemen Pengguna** - Kelola user admin
- **Manajemen Pengaduan** - Handle pengaduan masyarakat
- **Manajemen SKM** - Kelola Surat Keterangan Miskin
- **Settings** - Konfigurasi aplikasi:
  - Application settings
  - Pengaduan handlers
  - Database backup & restore
  - System logs

## 🛠️ Teknologi yang Digunakan

### Backend
- **Laravel 12** - PHP Framework
- **PHP 8.2+** - Bahasa pemrograman
- **MySQL/MariaDB** - Database
- **Eloquent ORM** - Object-relational mapping

### Frontend
- **Vite 5** - Build tool & dev server
- **Tailwind CSS 3** - Utility-first CSS framework
- **Alpine.js** - Lightweight JavaScript framework
- **Font Awesome 6** - Icon library
- **CKEditor 5** - Rich text editor

### Tools & Libraries
- **Composer** - PHP dependency manager
- **npm** - Node.js package manager
- **Laravel Sanctum** - API authentication

## 📋 Persyaratan Sistem

- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 18.x
- **npm** >= 9.x
- **MySQL** >= 8.0 atau **MariaDB** >= 10.6
- **Web Server** (Apache/Nginx)
- **PHP Extensions**:
  - BCMath
  - Ctype
  - cURL
  - DOM
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PCRE
  - PDO
  - Tokenizer
  - XML
  - GD

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/your-username/perijinan.git
cd perijinan
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Konfigurasi Environment

```bash
# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perijinan
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Migrasi Database

```bash
# Jalankan migrasi
php artisan migrate

# (Opsional) Seed data dummy
php artisan db:seed
```

### 6. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Storage Link

```bash
php artisan storage:link
```

### 8. Jalankan Aplikasi

```bash
# Menggunakan Laravel development server
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## 📁 Struktur Database

### Tabel Utama

| Tabel | Deskripsi |
|-------|-----------|
| `users` | Data pengguna (admin & pemohon) |
| `perijinan` | Data jenis perizinan |
| `perijinan_form_fields` | Form field dinamis untuk perijinan |
| `perijinan_validation_flows` | Alur validasi perijinan |
| `berita` | Berita dan pengumuman |
| `regulasi` | Database regulasi/peraturan |
| `pengaduan` | Pengaduan masyarakat |
| `skm` | Surat Keterangan Miskin |
| `opd` | Organisasi Perangkat Daerah |
| `activity_logs` | Log aktivitas sistem |

## 🔐 Role User

### Admin
- Akses penuh ke semua fitur sistem
- URL: `/dashboard`

### Pemohon
- Akses terbatas untuk pengajuan perizinan
- URL: `/pemohon/dashboard`

### Front-End (Publik)
- Akses tanpa login untuk informasi umum
- URL: `/`

## 📸 Screenshots

### Landing Page
```
┌─────────────────────────────────────┐
│  🏛️ JITU Banjarnegara              │
│  Jasa Izin Terpadu Unggulan         │
├─────────────────────────────────────┤
│  📋 Layanan    📰 Informasi        │
│  📢 Pengaduan   📄 SKM Online       │
└─────────────────────────────────────┘
```

### Dashboard Pemohon
```
┌─────────────────────────────────────┐
│  👤 Dashboard Pemohon               │
├─────────────────────────────────────┤
│  📊 Statistik Pengajuan             │
│  📝 Daftar Pengajuan                │
│  ➕ Ajukan Izin Baru                │
└─────────────────────────────────────┘
```

### Dashboard Admin
```
┌─────────────────────────────────────┐
│  ⚙️ Dashboard Admin                 │
├─────────────────────────────────────┤
│  📈 Statistik                       │
│  📋 Manajemen Perijinan             │
│  📰 Manajemen Berita                │
│  👥 Manajemen User                  │
└─────────────────────────────────────┘
```

## 🎯 Fitur Unggulan

### 1. Form Builder Dinamis
Admin dapat membuat form custom untuk setiap jenis perizinan dengan berbagai tipe field:
- Text, Textarea, Number
- Date, Email, Phone
- Select, Radio, Checkbox
- File Upload

### 2. Alur Validasi Kustom
Setiap perijinan dapat memiliki alur validasi yang berbeda:
- Multi-level approval
- Assignment ke role spesifik
- SLA (Service Level Agreement) per tahap

### 3. Slider Berita
Berita dapat ditampilkan sebagai slider di homepage dengan toggle on/off langsung dari tabel admin.

### 4. Rich Text Editor
CKEditor 5 dengan fitur lengkap untuk konten berita dan persyaratan perijinan.

### 5. Backup & Restore
Sistem backup database dan aplikasi terintegrasi di dashboard admin.

## 🧪 Testing

```bash
# Run unit tests
php artisan test

# Run with coverage
php artisan test --coverage
```

## 📚 Dokumentasi API

API documentation tersedia di `/api/documentation` (jika menggunakan Swagger/OpenAPI).

## 🔒 Keamanan

- Password hashing dengan bcrypt
- CSRF protection
- SQL injection prevention
- XSS protection
- Role-based access control
- Input validation

## 🤝 Kontribusi

Terima kasih atas pertimbangan Anda untuk berkontribusi pada proyek ini! Jika Anda ingin berkontribusi, silakan:

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 Lisensi

Proyek ini dilisensikan di bawah [Lisensi MIT](https://opensource.org/licenses/MIT).

## 👥 Tim Pengembang

Dikembangkan untuk **DPMPTSP Kabupaten Banjarnegara**

## 📞 Kontak

Untuk pertanyaan atau dukungan, silakan hubungi:

- **Email**: admin@dpmptsp.banjarnegarakab.go.id
- **Website**: https://dpmptsp.banjarnegarakab.go.id
- **Alamat**: Jl. Raya Banjarnegara-Wonosobo KM 2, Banjarnegara, Jawa Tengah

## 🙏 Ucapan Terima Kasih

- Laravel Team - Framework yang luar biasa
- Tailwind CSS - CSS framework yang produktif
- Font Awesome - Icon library yang lengkap
- CKEditor - Editor yang powerful
- Semua kontributor dan pihak yang terlibat

---

<p align="center">
  Dibuat dengan ❤️ untuk pelayanan publik yang lebih baik
</p>
