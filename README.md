# 🏥 MEDTRACK-IPSRS
## Sistem Tracking, Pengelolaan Aset, dan Maintenance Peralatan Medis

Aplikasi web untuk Unit IPSRS (Instalasi Pemeliharaan Sarana Rumah Sakit) dan Logistik Medis dalam mengelola peralatan medis secara real-time dengan teknologi QR Code dan GPS Tracking.

---

## 📋 Fitur Utama

### ✅ P0 - MVP (Minimum Viable Product)
- **Authentication** - Login 3 role (Admin IPSRS, Staf Logistik, Staf Unit)
- **CRUD Aset** - Manajemen data peralatan medis lengkap
- **QR Code Generator** - Auto-generate QR untuk setiap aset
- **QR Scanning** - Scan untuk info alat & tracking lokasi
- **GPS Tracking** - Real-time lokasi dengan Leaflet Map
- **Tracking History** - Riwayat pergerakan aset

### 🔄 P1 - Enhancement (Week 2+)
- Manajemen sirkulasi/peminjaman aset
- Jadwal maintenance & reminder otomatis
- Laporan kerusakan dari staf unit

### 📊 P2 - Advanced (Week 3+)
- Export PDF & Excel
- Cetak label QR sticker
- Dashboard analytics & charts

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | PHP 7.4+ (OOP, PDO) |
| **Frontend** | Bootstrap 5, HTML5, JavaScript |
| **Database** | MySQL 5.7+ |
| **Mapping** | Leaflet.js |
| **QR Code** | PHPQRCode Library |
| **Geo-location** | HTML5 Geolocation API |
| **Version Control** | Git/GitHub |

---

## 📦 Folder Structure

```
medtrack-ipsrs/
├── config/                 # Konfigurasi (database, constants, security)
├── app/
│   ├── models/            # Database layer (User, Aset, Tracking, dll)
│   ├── controllers/       # Request handling
│   └── views/            # HTML templates
├── public/               # Assets & entry point
│   ├── index.php         # Router
│   ├── css/
│   ├── js/
│   └── uploads/
├── database/             # SQL schema & migrations
├── .gitignore
└── README.md
```

---

## 🚀 Quick Start

### Prerequisites
- PHP 7.4+ (dengan PDO MySQL)
- MySQL 5.7+ atau MariaDB
- XAMPP, WAMP, atau LAMP stack
- Git (untuk version control)

### Installation

1. **Clone Repository**
```bash
git clone https://github.com/yourusername/medtrack-ipsrs.git
cd medtrack-ipsrs
```

2. **Setup Folder Structure**
```bash
# Jalankan bash script (Linux/Mac) atau buat manual (Windows)
bash QUICK_START.sh
```

3. **Create Database**
```bash
# Buka phpMyAdmin atau command line
CREATE DATABASE medtrack_ipsrs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

4. **Import Database Schema**
```bash
# phpMyAdmin: Import → pilih database/schema.sql
# Atau via command line:
mysql -u root -p medtrack_ipsrs < database/schema.sql
mysql -u root -p medtrack_ipsrs < database/dummy-data.sql
```

5. **Configure Database Connection**
```bash
# Edit: config/database.php
# Update: $host, $db, $user, $pass sesuai setup local
```

6. **Access Application**
```
http://localhost/medtrack-ipsrs/public
```

7. **Login Credentials** (dari dummy data)
```
Admin:
- Username: admin1
- Password: password123

Staf Logistik:
- Username: logistik1
- Password: password123

Staf Unit:
- Username: unit1
- Password: password123
```

---

## 📊 Database Schema

### Core Tables (Master + Transaksi)

| Table | Purpose | Rows |
|-------|---------|------|
| **m_ruangan** | Master ruangan (IGD, ICCU, OK, dll) | 10 |
| **m_user** | User dengan role operasional sistem | 5 |
| **m_aset** | Peralatan medis + metadata kalibrasi | 50 dummy |
| **m_vendor** | Supplier/vendor | 5 dummy |
| **m_pemeliharaan** | Master jadwal preventive maintenance | Dynamic |
| **t_tracking** | GPS location history | Dynamic |
| **t_sirkulasi** | Peminjaman aset | Dynamic |
| **t_mutasi** | Mutasi/pergerakan aset antar ruangan | Dynamic |
| **t_maintenance** | Maintenance/servis berbasis aset | Dynamic |
| **t_troubleshoot** | Ticket work order kerusakan | Dynamic |
| **t_troubleshoot_log** | Riwayat perubahan status ticket | Dynamic |
| **t_pemeliharaan_log** | Riwayat pelaksanaan preventive maintenance | Dynamic |

---

## 🔐 Security Features

✅ **Password Hashing** - bcrypt encryption  
✅ **CSRF Token** - Form protection  
✅ **PDO Prepared Statements** - SQL injection prevention  
✅ **Session Management** - Secure auth handling  
✅ **Input Validation** - Server-side validation  
✅ **Role-Based Access Control** - Role permissions per modul  

---

## 📱 Supported Browsers

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile browsers (Android Chrome, iOS Safari)

---

## 🧪 Testing

### Manual Testing Checklist

- [ ] Login dengan semua 3 roles
- [ ] Create, Edit, Delete aset
- [ ] Generate & download QR code
- [ ] Scan QR code
- [ ] GPS tracking & map display
- [ ] Form validation
- [ ] Responsive design (mobile/tablet)

### Browser DevTools
```javascript
// Check console untuk errors
// Check Network tab untuk API requests
// Check Application tab untuk localStorage
```

---

## 📖 Usage Guide

### For Admin IPSRS

1. **Dashboard** - Lihat overview aset & status
2. **Kelola Aset** - CRUD aset, generate QR
3. **Tracking Map** - Monitor lokasi real-time
4. **Maintenance** - Jadwal & log maintenance
5. **Report** - Export PDF/Excel (coming soon)

### For Staf Logistik

1. **Input Aset Baru** - Register aset ke sistem
2. **Mutasi** - Pencatatan pergerakan antar ruang
3. **Verifikasi Stok** - Check ketersediaan aset

### For Staf Unit

1. **Scan QR** - Input lokasi saat ada pemindahan
2. **Info Aset** - Lihat detail spek alat
3. **Lapor Kerusakan** - Report maintenance needed

---

## 🐛 Troubleshooting

### Database Connection Error
```
Error: SQLSTATE[HY000]: General error
Solusi:
1. Check database.php config
2. Pastikan MySQL running
3. Create database: medtrack_ipsrs
4. Import schema.sql
```

### QR Code Not Generating
```
Error: QR image not found
Solusi:
1. Check /public/uploads/qr/ folder exists
2. Chmod 755 untuk upload folder
3. Verify PHPQRCode library installed
```

### Geolocation Not Working
```
Error: Allow location access denied
Solusi:
1. Check browser permission
2. HTTPS recommended (localhost OK untuk development)
3. Check HTML5 Geolocation API support
4. Fallback: manual input lokasi
```

### CSRF Token Error
```
Error: CSRF token mismatch
Solusi:
1. Clear browser cache/cookies
2. Refresh page & try again
3. Check session timeout
```

---

## 📝 Development Notes

### Code Style
- PHP: PSR-12 Coding Standards
- Naming: camelCase untuk method, snake_case untuk database
- Comments: English untuk dokumentasi

### Version Control
```bash
# Daily commit
git add .
git commit -m "feat: implement tracking GPS"
git push origin main

# Branching strategy
main → develop → feature/branch
```

### File Naming
```
Controllers: AuthController.php (CamelCase)
Models: User.php (CamelCase)
Views: login.php (snake-case)
```

---

## 🔄 Deployment

### Local to Production

1. **Prepare Files**
   - Remove .env.example
   - Set proper file permissions
   - Exclude uploads/ dari git

2. **Database**
   - Backup database sebelum deployment
   - Import schema & data ke production

3. **Server Config**
   - Enable mod_rewrite (Apache)
   - PHP 7.4+ installed
   - MySQL access available

4. **HTTPS**
   - Install SSL certificate
   - Update BASE_URL di constants.php

---

## 📞 Support & Contact

**Developed for:** Unit IPSRS & Logistik Medis Rumah Sakit  
**Framework:** Custom MVC (Vanilla PHP)  
**License:** MIT  
**Last Updated:** 2024  

---

## ✨ Roadmap 2024

- [x] MVP Development (Week 1)
- [ ] Sirkulasi & Maintenance (Week 2)
- [ ] Reporting & Export (Week 3)
- [ ] Optimization & Security (Week 4)
- [ ] Mobile App (Q2 2024)

---

## 📄 License

MIT License - Feel free to use for educational & commercial projects

---

## 👨‍💻 Contributors

- **Lead Developer:** Your Name
- **Tech Lead:** Mentor Name
- **Client:** Unit IPSRS Rumah Sakit

---

**Made with ❤️ for better asset management**
