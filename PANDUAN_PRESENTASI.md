# 🏥 Panduan Detail Pemahaman Teknis Projek MedTrack
*Materi Pendalaman Struktur Kode, Alur Data, Otorisasi Peran, dan Integrasi Laravel + React Inertia untuk Persiapan Presentasi & Sidang*

---

## 📂 BAB I: Bedah Struktur Direktori Proyek (Laravel + React Inertia)
Dosen sering menanyakan bagaimana backend dan frontend menyatu. Proyek ini menggunakan arsitektur **Hybrid Monolith** dengan **Inertia.js** sebagai jembatan.

### 1. Sisi Backend (Laravel 10)
*   **`app/Models/`**: Berisi file representasi tabel database (ORM Eloquent):
    *   `User.php` (Tabel `m_user`): Data pengguna, otorisasi role, dan relasi ruangan.
    *   `Aset.php` (Tabel `m_aset`): Data inventaris alat medis, lokasi koordinat tracking, tanggal kalibrasi, dan QR Code.
    *   `Ruangan.php` (Tabel `m_ruangan`): Kamar/Unit di rumah sakit (ICU, IGD, dll.).
    *   `Troubleshoot.php` (Tabel `t_troubleshoot`): Tiket laporan kerusakan (*Work Order*).
    *   `TroubleshootLog.php` (Tabel `t_troubleshoot_log`): Riwayat perkembangan status perbaikan.
    *   `Mutasi.php` (Tabel `t_mutasi`): Log dan permohonan perpindahan aset antar-ruangan.
    *   `Pemeliharaan.php` & `PemeliharaanLog.php`: Pengaturan jadwal pemeliharaan preventif (PM).
    *   `DokumenMutu.php`: Pengarsipan file laporan bulanan/mutu.
*   **`app/Http/Controllers/Web/`**: Logika bisnis dan pemrosesan data web:
    *   `DashboardController.php`: Menghitung statistik dasbor, merender kalender harian, dan reschedule pemeliharaan.
    *   `AsetController.php`: CRUD aset medis, filter otomatis per-unit untuk Unit_RS, dan proteksi hak akses tambah/edit/hapus.
    *   `WorkOrderController.php`: Manajemen pengaduan kerusakan, assign teknisi, dan update status.
    *   `MutasiController.php`: Pengajuan mutasi alat medis dan persetujuan (Approve/Reject).
    *   `DirektoriController.php`: CRUD ruangan dan manajemen SDM/Staff.
*   **`app/Http/Middleware/HandleInertiaRequests.php`**: Middleware krusial untuk berbagi data secara global di frontend (seperti data notifikasi di lambang lonceng header dan agenda kalender sidebar).
*   **`routes/web.php`**: Rute web yang dilindungi auth session.
*   **`routes/api.php`**: Endpoint REST API untuk integrasi pelacakan mobile Android (menggunakan Laravel Sanctum).

### 2. Sisi Frontend (React.js + Inertia)
*   **`resources/js/Pages/`**: Halaman antarmuka web (Views):
    *   `Dashboard.jsx`: Dasbor statistik, grafik interaktif ChartJS, dan panel notifikasi.
    *   `Aset/`: Manajemen aset medis, QR Code, dan peta pelacakan posisi.
    *   `WorkOrder/`: Daftar penugasan teknisi dan status tiket perbaikan.
    *   `Mutasi/`: Form mutasi alat medis dan tombol aksi verifikasi.
    *   `Direktori/`: Profil staff dan ruangan rumah sakit.
*   **`resources/js/Layouts/AuthenticatedLayout.jsx`**: Layout utama pembungkus halaman (Sidebar menu, header, modal scanner QR Code, kalender agenda harian, dan lonceng notifikasi dinamis).
*   **`resources/js/Components/`**: Komponen reusable (Dropdown, Modal, TextInput, PrimaryButton).

---

## 🔄 BAB II: Alur Pertukaran Data (Inertia.js & REST API)

### 1. Siklus Web Request (Inertia.js - Stateful)
Tidak ada REST API internal untuk web browser. Protokol berjalan secara stateless namun terasa stateful:
1. Pengguna mengklik menu **"Aset Medis"** di Sidebar.
2. Inertia mengirimkan request XHR (AJAX) di latar belakang ke `/aset`.
3. Laravel memproses di `AsetController@index`, mengambil data aset, lalu mengembalikan rendering:
   ```php
   return Inertia::render('Aset/Index', ['asets' => $asets]);
   ```
4. Inertia di frontend menangkap payload data JSON tersebut, lalu mengganti isi halaman React (`Aset/Index.jsx`) secara instan **tanpa me-reload seluruh halaman browser**.

### 2. Siklus Mobile API Request (Laravel Sanctum - Stateless)
Digunakan untuk komunikasi dengan aplikasi mobile Android pelacak lokasi (Kotlin):
1. Aplikasi Android mengirim POST request ke `/api/login` membawa email & password.
2. Jika cocok, Laravel Sanctum membuat token acak di tabel `personal_access_tokens` dan mengembalikannya dalam bentuk JSON.
3. Android menyimpan token ini di memori lokal. Setiap kali Android mengirim data koordinat GPS aset ke `/api/tracking`, ia harus menyertakan header `Authorization: Bearer <token>`.

---

## 🛡️ BAB III: Logika Otorisasi Peran (Role-Based Access Control)
Sistem memiliki 3 role utama yang dikelola secara dinamis di backend dan frontend:

1.  **IPSRS (Admin_IPSRS & Staf_IPSRS):**
    *   **Wewenang:** Hak penuh (CRUD) aset medis, reschedule kalibrasi, menugaskan teknisi, menyetujui/menolak mutasi.
    *   **Pengecualian Teknisi Lapangan (Budi, Hendra, Agus):** Username `budi_ipsrs`, `hendra_ipsrs`, dan `agus_ipsrs` dibatasi secara otomatis di controller sehingga **tidak bisa melakukan CRUD aset/ruangan/SDM** dan **tidak bisa menugaskan teknisi**. Mereka hanya bisa memperbarui status pengerjaan Work Order (WO) yang ditugaskan kepada mereka.
2.  **Staf_Logistik:**
    *   **Wewenang:** Membantu pencatatan/tambah aset baru (`create` & `store`), mengelola dokumen mutu laporan bulanan, dan direktori SDM.
    *   **Pembatasan:** **Tidak boleh mengedit atau menghapus aset** (tombol Edit/Hapus disembunyikan dan diblokir di backend), serta tidak bisa memverifikasi mutasi.
3.  **Unit_RS (Kepala/Staf Ruangan seperti ICU, IGD):**
    *   **Tampilan Terbatas:** Default halaman Aset Medis hanya menampilkan alat di ruangannya saja.
    *   **Wewenang:** Melaporkan kerusakan (Work Order), mengajukan mutasi alat keluar dari ruangannya.
    *   **Pencarian Global (Scan QR):** Jika melakukan pencarian atau pemindaian QR Code, filter unit dimatikan sementara agar mereka dapat melihat detail aset di ruangan lain.

---

## 🔔 BAB IV: Bedah Logika Kritis Kodingan

### 1. Notifikasi Lonceng Dinamis di Layout Header
Logika ini diletakkan di `HandleInertiaRequests.php` agar dapat tampil di semua halaman secara dinamis:
```php
'notifications' => function () use ($request) {
    $user = $request->user();
    if (!$user) return [];
    
    $role = $user->role;
    $notifications = [];
    
    if ($role === 'Admin_IPSRS' || $role === 'Staf_IPSRS') {
        // Notifikasi PM Terjadwal, Mutasi Pending, dan Laporan Kerusakan Baru (Open)
        $scheduledCount = PemeliharaanLog::where('status_pelaksanaan', 'Terjadwal')->count();
        $pendingMutasiCount = Mutasi::where('status_mutasi', 'Menunggu_Verifikasi')->count();
        $openWOCount = Troubleshoot::where('status_ticket', 'Open')->count();
        // ... build notification arrays
    }
    // ... logic for Logistik and Unit_RS
    return $notifications;
}
```
*   **Fungsi Dismiss (Klik Hilang):** Di frontend `AuthenticatedLayout.jsx`, ketika notifikasi diklik, ID notifikasi disimpan di `localStorage` (`dismissed_notifs`) sehingga item tersebut **langsung hilang** dari lonceng secara *real-time* dan pengguna langsung dialihkan ke form tujuan.

### 2. Sinkronisasi Tanggal Kalibrasi & Tanggal Kadaluarsa Sertifikat
Ketika admin menyimpan data aset baru atau melakukan reschedule pada kalender, sistem secara otomatis menghitung sinkronisasi tanggal di `DashboardController.php` dan `AsetController.php`:
```php
$aset->update([
    'tgl_kalibrasi_terakhir' => $newDate,
    'tgl_kadaluarsa_sertif' => $newDate // sinkron otomatis
]);
```

### 3. Batasan Mutasi Lintas Ruangan
*   Di `MutasiController.php` `create()`, query aset diatur `whereNotNull('id_ruang_saat_ini')` agar semua unit bebas memilih aset mana saja di rumah sakit untuk ditarik/dimutasi ke ruangan mereka.

---

## 👨‍🏫 BAB V: Tips Menjawab Pertanyaan Dosen Penguji

1.  **Tanya: "Bagaimana integrasi dengan Android bekerja? Apakah memakai Session?"**
    *   *Jawab:* "Tidak, Pak. Untuk Android kami menggunakan **REST API Stateless** yang diamankan oleh **Laravel Sanctum**. Android mengirimkan data koordinat GPS ke server dengan melampirkan *Bearer Token* di header Authorization pada setiap request HTTP."
2.  **Tanya: "Mengapa teknisi seperti Budi tidak bisa menghapus aset?"**
    *   *Jawab:* "Kami menerapkan prinsip **Least Privilege** pada Otorisasi Peran. Teknisi lapangan fokus pada perbaikan alat di lapangan, sehingga hak CRUD aset medis dibatasi demi keamanan data inventaris agar tidak disalahgunakan. Mereka hanya diberikan hak mengubah status Work Order."
3.  **Tanya: "Bagaimana cara kerja pencarian aset via scanner QR Code?"**
    *   *Jawab:* "Di frontend (`AuthenticatedLayout.jsx`), kami mengintegrasikan library kamera HTML5. Hasil scan berupa teks kode label (seperti `MED-003`) akan dicari secara global di backend. Jika cocok, sistem langsung mengalihkan (redirect) ke halaman detail aset terkait."
4.  **Tanya: "Di mana Anda mengatur data notifikasi lonceng agar selalu update di setiap halaman?"**
    *   *Jawab:* "Kami membagikannya secara global melalui shared props di middleware **`HandleInertiaRequests.php`**. Data ini otomatis terkirim pada setiap respon Inertia dan di-render oleh komponen Layout utama."

---
*Good luck untuk presentasinya besok! File ini siap dipelajari secara detail langsung di editor kode Anda.*
