# 🔧 SETUP & MIGRATION GUIDE - MEDTRACK-IPSRS FRAMEWORK

Panduan lengkap migrasi proyek dari **Native PHP** ke **Laravel + Inertia.js (React)**, setup lingkungan lokal, testing API menggunakan **Postman**, serta integrasi dengan aplikasi **Android Kotlin**.

---

## 📋 Prerequisites (Prasyarat Sistem)

Sebelum memulai, pastikan perangkat Anda sudah menginstall tool berikut:

- [ ] **Laragon** (Sangat Direkomendasikan) atau **XAMPP**
  - Download Laragon: https://laragon.org/download/
  - Minimum PHP: **PHP 8.1+** (disarankan PHP 8.2)
  - Database: **MySQL 5.7+** atau **MariaDB**
- [ ] **Node.js** (LTS version, minimal v18.x)
  - Download: https://nodejs.org/
  - Digunakan untuk compiler assets frontend (React & Tailwind via Vite)
- [ ] **Composer**
  - Download: https://getcomposer.org/
  - Dependency manager untuk PHP/Laravel
- [ ] **Git** & **VS Code**
- [ ] **Postman** (untuk menguji API)
  - Download: https://www.postman.com/downloads/

---

## 📂 Step 1: Cadangkan File UTS Native

Sebelum menginstall Laravel di direktori root `c:/laragon/www/Medtrack-IPSRS_Framework`, pindahkan file UTS Native ke folder cadangan:

1. Buat folder baru bernama `uts_native` di root project.
2. Pindahkan folder berikut ke dalam `uts_native/`:
   - `app/`
   - `config/`
   - `public/`
   - `database/`
3. Pindahkan juga file `.gitignore`, `README.md`, `SETUP.md`, `FILE_MAPPING.md`, `debug_*.php`, dan `regenerate_qr.php` (jika ada) ke dalam `uts_native/` (catatan: file readme/setup/mapping baru yang sedang Anda baca ini akan menggantikannya di root).

*Sekarang, direktori root proyek Anda harus dalam keadaan bersih/kosong, menyisakan folder `uts_native/` saja.*

---

## 🚀 Step 2: Install Laravel & Breeze (React/Inertia)

Buka Terminal/PowerShell di folder `c:/laragon/www/Medtrack-IPSRS_Framework/` dan ikuti perintah di bawah ini:

### 1. Inisialisasi Project Laravel
Karena folder saat ini tidak kosong (ada `uts_native`), kita unduh Laravel ke folder sementara lalu pindahkan isi file-nya, atau gunakan perintah instalasi composer dengan parameter:
```bash
# Jalankan perintah composer untuk inisialisasi Laravel di folder saat ini
composer create-project laravel/laravel . --stability=stable --keep-vcs
```
*Catatan: Jika ada konflik file, pastikan folder root benar-benar kosong selain folder `uts_native`.*

### 2. Install Laravel Breeze (Starter Kit Auth)
Breeze secara otomatis mengkonfigurasi TailwindCSS, React, Vite, Inertia, dan sistem login multi-user untuk kita.
```bash
composer require laravel/breeze --dev
```

### 3. Install Package React + Inertia
Jalankan perintah interaktif berikut:
```bash
php artisan breeze:install react
```
*Pilih opsi default ketika ditanya (menggunakan SSR atau TypeScript tidak wajib, pilih `No` jika ingin JavaScript React biasa).*

---

## 🗄️ Step 3: Setup Database di Laravel

1. Buka file `.env` di VS Code root project.
2. Konfigurasi kredensial database Anda seperti contoh berikut:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=medtrack_ipsrs
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   *(Kosongkan DB_PASSWORD jika Anda menggunakan XAMPP/Laragon default)*.

3. Jalankan migrasi dan seeder awal (apabila migrasi telah disiapkan):
   ```bash
   php artisan migrate --seed
   ```

---

## ⚙️ Step 4: Menjalankan Aplikasi (Development)

Untuk menjalankan aplikasi ini secara lokal, Anda harus mengaktifkan server **Backend (Laravel)** dan compiler **Frontend (Vite)** secara bersamaan:

### Terminal 1: Laravel Backend
```bash
php artisan serve
```
Server akan berjalan di `http://127.0.0.1:8000`

### Terminal 2: React Vite Compiler
```bash
npm run dev
```
Ini akan memantau perubahan pada file React di `resources/js/` secara real-time.

Buka browser Anda dan akses `http://127.0.0.1:8000`. Anda akan melihat halaman selamat datang Laravel dengan tombol **Log in** di kanan atas.

---

## 📲 Step 5: Setup REST API untuk Android Kotlin

Agar aplikasi Android Kotlin Anda dapat berkomunikasi dengan aman, kita akan menggunakan **Laravel Sanctum** (sudah otomatis terinstall lewat Laravel Breeze).

### Rute API (`routes/api.php`)
Pastikan endpoint API terdaftar di `routes/api.php` agar dapat diakses tanpa proteksi web session (Stateful).

```php
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiAsetController;

// Rute Publik (Tanpa Login)
Route::post('/login', [ApiAuthController::class, 'login']);

// Rute Terproteksi Token (Harus Mengirimkan Header: Authorization Bearer <token>)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/aset/{qr_code}', [ApiAsetController::class, 'getDetailByQr']);
    Route::post('/tracking', [ApiAsetController::class, 'updateLocation']);
});
```

---

## 🧪 Step 6: Cara Uji API menggunakan Postman

Buka aplikasi **Postman** di PC Anda untuk melakukan pengujian endpoint sebelum menulis kode Kotlin di Android:

### 1. Test Login & Dapatkan Token
*   **Method:** `POST`
*   **URL:** `http://127.0.0.1:8000/api/login`
*   **Headers:** `Accept: application/json`
*   **Body (raw JSON):**
    ```json
    {
        "username": "admin1",
        "password": "password123"
    }
    ```
*   **Hasil Response:**
    ```json
    {
        "status": "success",
        "token": "1|abcdefghij1234567890...",
        "user": {
            "name": "Admin IPSRS",
            "role": "admin"
        }
    }
    ```
    *Copy string token yang Anda dapatkan.*

### 2. Test Get Detail Aset (Scan QR)
*   **Method:** `GET`
*   **URL:** `http://127.0.0.1:8000/api/aset/QR-DFB-012`
*   **Headers:**
    *   `Accept: application/json`
    *   `Authorization: Bearer <GANTI_DENGAN_TOKEN_YANG_TADI_DICOPY>`
*   **Hasil Response:** Data detail alat medis dari MySQL.

### 3. Test Update Lokasi GPS (Android Tracking)
*   **Method:** `POST`
*   **URL:** `http://127.0.0.1:8000/api/tracking`
*   **Headers:**
    *   `Accept: application/json`
    *   `Authorization: Bearer <TOKEN>`
*   **Body (raw JSON):**
    ```json
    {
        "qr_code": "QR-DFB-012",
        "latitude": -6.2088,
        "longitude": 106.8456,
        "id_ruang": 3
    }
    ```

---

## 📱 Step 7: Integrasi ke Aplikasi Android Kotlin

Pada proyek Kotlin Android Studio Anda:

1.  **Tambahkan Permission Internet** pada `AndroidManifest.xml`:
    ```xml
    <uses-permission android:name="android.permission.INTERNET" />
    <uses-permission android:name="android.permission.ACCESS_FINE_LOCATION" />
    ```
2.  **Konfigurasi Retrofit Client**:
    *Catatan: Di emulator Android, gunakan alamat IP khusus `10.0.2.2` untuk mengakses localhost komputer Anda (bukan 127.0.0.1).*
    ```kotlin
    val BASE_URL = "http://10.0.2.2:8000/api/"
    ```
3.  **Kirim Token di Setiap Request**:
    Simpan token yang didapat dari API login ke `SharedPreferences` atau `DataStore`, lalu masukkan token tersebut sebagai Header `Authorization: Bearer <token>` di setiap request Retrofit untuk mengakses data aset dan tracking.

---

## 🐛 Troubleshooting

*   **Error: `Vite manifest not found` di Browser:**
    Pastikan compiler frontend Anda berjalan. Jalankan `npm run dev` di terminal kedua Anda.
*   **Error: `419 Page Expired` saat POST Form di Web:**
    Laravel memerlukan token CSRF untuk keamanan web. Jika menggunakan Inertia, hal ini sudah ditangani otomatis. Jika menggunakan Ajax biasa, pastikan menyertakan header `X-CSRF-TOKEN`.
*   **Error: `CORS Policy: Request header field Authorization is not allowed...`:**
    Pastikan file `config/cors.php` Anda di backend sudah mengizinkan header `Authorization` dan `Content-Type` untuk domain client Anda.
