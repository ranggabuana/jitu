# 📧 Email Service & Forgot Password Documentation

Dokumentasi ini menjelaskan implementasi layanan email terpusat dan fitur lupa password pada aplikasi **JITU Banjarnegara**.

---

## 🚀 EmailService (Abstract Layer)
Layanan ini diabstraksikan di `app/Services/EmailService.php` untuk mempermudah pengiriman email dengan logging otomatis dan penanganan error.

### Cara Penggunaan:
Gunakan class ini untuk mengirim email dari controller atau service manapun.

```php
use App\Services\EmailService;
use App\Mail\YourMailableClass;

// 1. Kirim Sinkron (Langsung)
EmailService::send(
    to: 'user@example.com',
    name: 'Nama Penerima',
    mailable: new YourMailableClass($data)
);

// 2. Masukkan ke Antrean (Queue/Async) - Direkomendasikan untuk performa
EmailService::queue(
    to: 'user@example.com',
    name: 'Nama Penerima',
    mailable: new YourMailableClass($data)
);
```

---

## 🔐 Fitur Lupa Password
Fitur ini memungkinkan pengguna mereset password melalui link yang dikirim ke email.

### Komponen Terkait:
1.  **Controller:** `app/Http/Controllers/Auth/ForgotPasswordController.php` (Logika utama)
2.  **Mailable:** `app/Mail/ForgotPasswordRequest.php` (Pengaturan amplop email)
3.  **Template Email:** `resources/views/emails/forgot-password.blade.php` (Desain HTML email)
4.  **Views:** 
    *   `resources/views/auth/forgot-password.blade.php` (Form minta link)
    *   `resources/views/auth/reset-password.blade.php` (Form input password baru)

### Fitur Keamanan:
*   **Token Expiry:** Link hanya berlaku selama **60 menit**.
*   **Hashed Tokens:** Token di-hash menggunakan `bcrypt` sebelum disimpan ke database.
*   **One-time Use:** Token langsung dihapus setelah password berhasil direset.
*   **Logging:** Setiap kegagalan atau keberhasilan pengiriman dicatat di `storage/logs/laravel.log`.

---

## 🛠 Konfigurasi SMTP (.env)

Agar email dapat terkirim, konfigurasi di file `.env` harus benar. Berikut adalah pengaturan untuk **Gmail**:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME="email-anda@gmail.com"
MAIL_PASSWORD="sandi-aplikasi-anda" # BUKAN password login biasa
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="email-anda@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Cara Mendapatkan `MAIL_PASSWORD` (Gmail):
1.  Buka [Akun Google](https://myaccount.google.com/).
2.  Aktifkan **2-Step Verification**.
3.  Cari menu **App Passwords** (Sandi Aplikasi).
4.  Buat sandi baru untuk aplikasi "Mail", lalu salin 16 karakter kode yang muncul.

---

## 🔍 Troubleshooting (Penyelesaian Masalah)

Jika Anda menemui error saat mengirim email, periksa hal-hal berikut:

### 1. Error: "The 'tls' scheme is not supported"
*   **Penyebab:** Anda menggunakan `MAIL_SCHEME=tls`.
*   **Solusi:** Hapus `MAIL_SCHEME` dan gunakan `MAIL_ENCRYPTION=tls`. Pastikan di `config/mail.php` bagian `smtp` menggunakan `'encryption' => env('MAIL_ENCRYPTION', 'tls')`.

### 2. Error: "Authentication Required"
*   **Penyebab:** Password salah atau belum menggunakan App Password.
*   **Solusi:** Pastikan `MAIL_PASSWORD` adalah 16 karakter kode dari App Password Google, bukan password login akun.

### 3. Perubahan .env Tidak Terdeteksi
*   **Penyebab:** Laravel menyimpan cache konfigurasi.
*   **Solusi:** Jalankan perintah berikut di terminal:
    ```bash
    php artisan config:clear
    ```

### 4. Cara Cek Error Detail
Jika muncul alert "Gagal mengirim email", segera cek baris terakhir di file:
`storage/logs/laravel.log`

---

## 🧪 Testing Development
Untuk testing tanpa mengirim email asli, Anda bisa menggunakan driver **log**:
```env
MAIL_MAILER=log
```
Email akan tertulis sebagai teks di file `storage/logs/laravel.log`.

---

## 📋 Best Practices
*   **Jangan membagikan `.env`** yang berisi `MAIL_PASSWORD` asli.
*   **Selalu gunakan `EmailService::queue()`** untuk pengiriman email masal agar aplikasi tidak terasa lambat bagi user.
*   **Gunakan Mailtrap** jika ingin testing UI email secara nyata tanpa mengganggu email orang lain.
