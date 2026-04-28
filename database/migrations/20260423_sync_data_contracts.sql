-- ============================================================================
-- MIGRATION: Sync Data Contracts (Schema <-> Model/Controller)
-- Date: 2026-04-23
-- ============================================================================

-- 1) Sinkron role user agar sesuai guard/controller saat ini
ALTER TABLE m_user
MODIFY COLUMN role ENUM('Admin_IPSRS', 'Staf_IPSRS', 'Staf_Logistik', 'Staf_Unit', 'Unit_RS', 'Kepala_IPSRS') NOT NULL;

-- Mapping data lama ke role baru
UPDATE m_user
SET role = 'Unit_RS'
WHERE role = 'Staf_Unit';

ALTER TABLE m_user
MODIFY COLUMN role ENUM('Admin_IPSRS', 'Staf_IPSRS', 'Staf_Logistik', 'Unit_RS', 'Kepala_IPSRS') NOT NULL;

-- 2) Sinkron kolom m_aset yang dipakai model/form
ALTER TABLE m_aset
ADD COLUMN kategori_aset ENUM('Medis', 'Sarpras', 'IT') DEFAULT 'Medis' AFTER nama_alat,
ADD COLUMN jumlah_unit INT NOT NULL DEFAULT 1 AFTER kategori_aset,
ADD COLUMN tgl_kalibrasi_terakhir DATE AFTER tgl_pengadaan,
ADD COLUMN gambar_aset VARCHAR(255) AFTER keterangan,
ADD COLUMN latitude DECIMAL(10, 8) AFTER gambar_aset,
ADD COLUMN longitude DECIMAL(11, 8) AFTER latitude;

ALTER TABLE m_aset
MODIFY COLUMN status_kondisi ENUM('Baik', 'Rusak_Ringan', 'Rusak_Berat', 'Maintenance', 'Gudang', 'Pensiun') DEFAULT 'Baik';

-- 3) Mutasi ruangan (dipakai Mutasi_model)
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

-- 4) Work Order enhancements (dipakai WorkOrder_model)
ALTER TABLE t_troubleshoot
ADD COLUMN id_teknisi_penanggungjawab INT NULL AFTER id_user_pelapor,
ADD INDEX idx_status_ticket (status_ticket),
ADD INDEX idx_teknisi (id_teknisi_penanggungjawab),
ADD CONSTRAINT fk_ttroubleshoot_teknisi
    FOREIGN KEY (id_teknisi_penanggungjawab) REFERENCES m_user(id_user) ON DELETE SET NULL;

CREATE TABLE IF NOT EXISTS t_troubleshoot_log (
  id_log INT PRIMARY KEY AUTO_INCREMENT,
  id_ticket INT NOT NULL,
  status_lama ENUM('Open', 'Pengecekan', 'Dikerjakan', 'Closed') NOT NULL,
  status_baru ENUM('Open', 'Pengecekan', 'Dikerjakan', 'Closed') NOT NULL,
  catatan TEXT,
  diubah_oleh INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_ticket) REFERENCES t_troubleshoot(id_ticket) ON DELETE CASCADE,
  FOREIGN KEY (diubah_oleh) REFERENCES m_user(id_user) ON DELETE SET NULL,
  INDEX idx_ticket (id_ticket),
  INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
