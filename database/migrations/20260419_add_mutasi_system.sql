-- ============================================================================
-- MIGRATION: Add Mutasi Ruangan (Asset Movement) System
-- Date: 2026-04-19
-- ============================================================================

-- Tabel Mutasi Ruangan (Pencatatan pergerakan aset antar ruangan)
CREATE TABLE IF NOT EXISTS t_mutasi (
  id_mutasi INT PRIMARY KEY AUTO_INCREMENT,
  id_aset INT NOT NULL,
  ruang_asal INT NOT NULL,
  ruang_tujuan INT NOT NULL,
  id_user_pencatat INT,
  tgl_mutasi DATETIME DEFAULT CURRENT_TIMESTAMP,
  alasan_mutasi VARCHAR(255),
  status_mutasi ENUM('Menunggu_Verifikasi', 'Disetujui', 'Ditolak', 'Selesai') DEFAULT 'Menunggu_Verifikasi',
  catatan TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (id_aset) REFERENCES m_aset(id_aset) ON DELETE CASCADE,
  FOREIGN KEY (ruang_asal) REFERENCES m_ruangan(id_ruang) ON DELETE RESTRICT,
  FOREIGN KEY (ruang_tujuan) REFERENCES m_ruangan(id_ruang) ON DELETE RESTRICT,
  FOREIGN KEY (id_user_pencatat) REFERENCES m_user(id_user) ON DELETE SET NULL,
  INDEX idx_status (status_mutasi),
  INDEX idx_tgl (tgl_mutasi),
  INDEX idx_aset (id_aset)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
