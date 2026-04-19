-- ============================================================================
-- MIGRATION: Add Maintenance & Prevention System
-- Date: 2026-04-19
-- ============================================================================

-- Tabel Master Pemeliharaan (Jadwal rutin infrastruktur/fasilitas)
CREATE TABLE IF NOT EXISTS m_pemeliharaan (
  id_pemeliharaan INT PRIMARY KEY AUTO_INCREMENT,
  nama_item VARCHAR(120) NOT NULL UNIQUE,
  deskripsi TEXT,
  lokasi VARCHAR(100),
  frekuensi ENUM('Harian', '2x_Harian', '3x_Harian', 'Mingguan', 'Bulanan', '3_Bulanan', '6_Bulanan', 'Tahunan') NOT NULL,
  pic_penanggung_jawab VARCHAR(100),
  catatan TEXT,
  status ENUM('Aktif', 'Nonaktif') DEFAULT 'Aktif',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_frekuensi (frekuensi),
  INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Log Pemeliharaan (History pelaksanaan maintenance)
CREATE TABLE IF NOT EXISTS t_pemeliharaan_log (
  id_log INT PRIMARY KEY AUTO_INCREMENT,
  id_pemeliharaan INT NOT NULL,
  id_user_pelaksana INT,
  tgl_pelaksanaan DATETIME DEFAULT CURRENT_TIMESTAMP,
  tgl_rencana DATE,
  status_pelaksanaan ENUM('Terjadwal', 'Terselesaikan', 'Tertunda', 'Dibatalkan') DEFAULT 'Terjadwal',
  hasil_pengecekan TEXT,
  kondisi_laporan VARCHAR(255),
  foto_dokumentasi VARCHAR(255),
  catatan_khusus TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (id_pemeliharaan) REFERENCES m_pemeliharaan(id_pemeliharaan) ON DELETE CASCADE,
  FOREIGN KEY (id_user_pelaksana) REFERENCES m_user(id_user) ON DELETE SET NULL,
  INDEX idx_tgl_rencana (tgl_rencana),
  INDEX idx_status (status_pelaksanaan),
  INDEX idx_pemeliharaan (id_pemeliharaan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert data pemeliharaan rutin
INSERT IGNORE INTO m_pemeliharaan (nama_item, deskripsi, lokasi, frekuensi, pic_penanggung_jawab, catatan) VALUES
('IPAL Backwash', 'Pencucian Filter dan Backwash IPAL', 'Water Treatment', '2x_Harian', 'Tim IPSRS', 'Pagi jam 06:00 dan Sore jam 16:00'),
('Cek Genset Pagi', 'Pengecekan Rutin Genset: Oli, Solar, Battery', 'Ruang Genset', 'Harian', 'Tim IPSRS', 'Dilakukan setiap pagi jam 07:00'),
('Penggantian Oksigen Central', 'Penggantian Tabung Oksigen di Ruang Central Oksigen (10 tabung)', 'Central Oksigen', '3x_Harian', 'Tim Pagi/Sore/Malam', 'Setiap 8 jam: Pagi (06:00), Sore (14:00), Malam (22:00)'),
('Cleaning AC', 'Pembersihan Filter dan Kalibrasi AC', 'Ruangan (sesuai area)', '3_Bulanan', 'Tim IPSRS', 'Jadwalkan setiap 3 bulan sekali'),
('Pembersihan Panel Listrik', 'Pembersihan Panel Listrik, Trafo, dan Kubikel', 'Ruang MER/Trafo', '6_Bulanan', 'Tim IPSRS', 'Dilakukan setiap 6 bulan dengan standar keselamatan');
