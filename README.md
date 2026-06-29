# 🏥 MedTrack - Sistem Informasi Pemeliharaan Alat Medis (IPSRS)

MedTrack adalah platform sistem informasi manajemen aset medis dan pemeliharaan alat kesehatan berbasis web untuk instalasi IPSRS (Instalasi Pemeliharaan Sarana Rumah Sakit). Aplikasi ini dibangun dengan arsitektur modern menggunakan **Laravel 10 + Inertia.js (React.js)** pada sisi web, serta mendukung integrasi pelacakan lokasi berbasis API untuk aplikasi mobile Android.

---

## 👥 Hak Akses & Peran Pengguna (Role-Based Access Control)

Sistem MedTrack membagi kewenangan pengguna ke dalam beberapa peran (*role*) yang spesifik untuk menjaga integritas data dan keamanan operasional rumah sakit:

### 1. 🛠️ **Admin_IPSRS** & **Staf_IPSRS** (Tim Teknis / Teknisi)
Merupakan peran pelaksana teknis harian yang memiliki wewenang paling luas terhadap pemeliharaan alat.
*   **Manajemen Aset:** Memiliki hak penuh untuk menambah, memperbarui, dan menghapus data aset medis serta mengunduh QR Code.
*   **Kalender Agenda:** Memantau semua jadwal pemeliharaan berkala, serta melakukan perubahan jadwal (**Reschedule**) kalibrasi/pemeliharaan langsung di kalender sidebar.
*   **Kerusakan & Work Order:** 
    *   Menerima tiket laporan kerusakan alat medis dari seluruh ruangan rumah sakit.
    *   Menunjuk dan menugaskan teknisi pelaksana perbaikan.
    *   Memperbarui perkembangan status perbaikan (*Sedang Dikerjakan*, *Selesai*).
*   **Mutasi Aset:** Memiliki wewenang mutlak untuk **menyetujui (Approve)** atau **menolak (Reject)** permohonan pemindahan alat medis antar-ruangan.

### 2. 🏥 **Unit_RS** (Kepala Ruangan / Staf Ruangan Medis seperti ICU, IGD, OK)
Merupakan peran operasional di lapangan yang menggunakan alat medis sehari-hari.
*   **Dashboard Terbatas:** Angka statistik dashboard, data aset medis, dan status laporan **dibatasi secara otomatis hanya untuk ruangan di mana staf tersebut ditugaskan** (misalnya, staf ICU hanya bisa melihat alat dan laporan di ruang ICU).
*   **Pelaporan Kerusakan:** Mengajukan laporan kerusakan alat medis yang berada di ruangannya dengan membuat tiket *Work Order* baru.
*   **Permohonan Mutasi:** Mengajukan perpindahan aset medis keluar dari ruangannya ke ruangan lain (status awal akan bernilai *Pending* menunggu persetujuan tim IPSRS).
*   **Pembatasan:** Tidak diperbolehkan mengedit data master aset medis, melakukan reschedule kalender kalibrasi, maupun menyetujui mutasi.

### 3. 📦 **Staf_Logistik** (Administrasi Inventaris)
Merupakan peran pendukung yang fokus pada administrasi inventaris aset dan dokumentasi.
*   **Pencatatan Aset:** Membantu menginput data pengadaan aset medis baru serta spesifikasi detailnya.
*   **Manajemen Direktori & Staf:** Mengelola direktori profil staf dan pengguna sistem di halaman direktori.
*   **Dokumen & Laporan:** Mengelola dan mengunduh berkas laporan bulanan IPSRS.
*   **Mutasi:** Dapat melihat log riwayat perpindahan aset medis secara keseluruhan untuk sinkronisasi inventaris, namun tidak memiliki hak melakukan *Approval*.

### 4. 👔 **Kepala_IPSRS** (Kepala Bagian IPSRS)
Merupakan peran struktural untuk fungsi monitoring dan evaluasi.
*   Memantau statistik keseluruhan aset medis di rumah sakit.
*   Melihat dokumen laporan performa pemeliharaan sarana prasarana.
*   Melihat daftar direktori staf IPSRS.

---

## 🔑 Akun Uji Coba Default

Untuk mempermudah pengujian, gunakan akun di bawah ini. Semua akun menggunakan kata sandi default: **`hasna123`**

| Peran (Role) | Username (Email) | Otoritas Utama |
| :--- | :--- | :--- |
| **Admin_IPSRS** | `admin_ipsrs@medtrack.com` | Full Akses, Reschedule Kalender, Approve/Reject Mutasi |
| **Staf_IPSRS** | `staf_ipsrs@medtrack.com` | Manajemen Work Order, Penugasan Teknisi |
| **Staf_Logistik** | `logistik@medtrack.com` | Edit Data Aset, Cetak Dokumen, Direktori Staf |
| **Unit_RS (ICU)** | `unit_icu@medtrack.com` | Lapor Kerusakan ICU, Ajukan Mutasi Aset ICU |
| **Kepala_IPSRS** | `kepala_ipsrs@medtrack.com` | Monitoring Laporan Bulanan & Direktori Staf |

---

## ✨ Fitur Utama MedTrack (Laravel-React)

1.  **Scanner QR Code Terintegrasi:** Scan label QR Code aset medis secara langsung menggunakan webcam/kamera laptop atau mengunggah gambar QR Code untuk pencarian aset instan.
2.  **Kalender Agenda Interaktif:** Kalender strip harian yang dilengkapi dot merah otomatis untuk tanggal yang memiliki agenda kalibrasi/pemeliharaan.
3.  **Reschedule Cepat:** Fitur penjadwalan ulang agenda pemeliharaan dengan sinkronisasi otomatis ke widget hitung mundur sisa hari.
4.  **Alur Mutasi Aset Premium:** Pengajuan mutasi yang rapi dengan status pill badge modern dan alur approve/reject sekali klik oleh admin.
5.  **Sanctum API Integrasi:** Dukungan endpoint REST API terproteksi token untuk integrasi dengan aplikasi pelacak lokasi Android.