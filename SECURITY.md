# Konfigurasi Keamanan Folder - Apache

Dokumen ini menjelaskan cara mengamankan folder `backups/` dan `uploads/perijinan/` agar tidak dapat diakses langsung melalui URL.

## 📁 Folder yang Diamankan

1. **`public/backups/`** - File backup database & aplikasi
2. **`public/uploads/perijinan/`** - File upload pengajuan perijinan

## 🔒 Cara Kerja

File-file di folder ini **hanya bisa diakses** melalui controller yang terproteksi dengan autentikasi admin:
- Backup: `/settings/backup/{type}/{filename}/download`
- Upload Perijinan: `/data-perijinan/download/{filename}`

---

## 🛠️ Konfigurasi Apache

File `.htaccess` sudah dibuat di masing-masing folder:
- `public/backups/.htaccess`
- `public/uploads/perijinan/.htaccess`

**Tidak perlu konfigurasi tambahan!** File `.htaccess` akan otomatis bekerja.

### Syarat:
- Module `mod_authz_core` harus aktif
- `AllowOverride` harus di-set ke `All` di konfigurasi Apache

---

## ✅ Verifikasi

Setelah konfigurasi, test dengan mengakses langsung:

### Test yang Harus Gagal (403 Forbidden):
```
https://domain-anda.com/backups/database/backup.sql
https://domain-anda.com/uploads/perijinan/5/file.pdf
```

### Test yang Harus Berhasil (dengan login admin):
```
https://domain-anda.com/settings/backup/database/backup.sql/download
https://domain-anda.com/data-perijinan/download/file.pdf
```

---

## 📝 Catatan Penting

### Apache
- Pastikan `AllowOverride All` di konfigurasi Apache
- Module `mod_authz_core` harus aktif
- File `.htaccess` otomatis diload

### Hosting Shared (cPanel, dll)
- Biasanya sudah configured dengan benar
- `.htaccess` langsung bekerja tanpa konfigurasi tambahan

### VPS/Cloud Server
- Cek konfigurasi Apache:
  ```apache
  <Directory /path/to/perijinan/public>
      AllowOverride All
  </Directory>
  ```
- Restart Apache setelah perubahan: `sudo systemctl restart apache2`

---

## 🔧 Troubleshooting

### File masih bisa diakses langsung
1. Cek `AllowOverride` di `httpd.conf` atau `apache2.conf`:
   ```apache
   <Directory /path/to/perijinan/public>
       AllowOverride All
   </Directory>
   ```
2. Restart Apache: `sudo systemctl restart apache2`

### Error 500 (Internal Server Error)
- Cek syntax `.htaccess`
- Cek error log: `/var/log/apache2/error.log`

### Clear Cache Browser
- Kadang browser menyimpan cache, clear cache dan coba lagi

---

## 📂 Struktur Folder

```
perijinan/
├── public/
│   ├── backups/
│   │   ├── .htaccess          ← Apache protection
│   │   ├── .gitignore
│   │   ├── database/
│   │   ├── aplikasi/
│   │   └── full/
│   └── uploads/
│       └── perijinan/
│           ├── .htaccess      ← Apache protection
│           └── {id}/
└── SECURITY.md                ← Dokumentasi ini
```

---

## 📞 Dukungan

Jika mengalami masalah, periksa:
1. Log error Apache
2. Permission file & folder
3. Konfigurasi `AllowOverride` sudah benar
4. Cache browser (clear cache)
